<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(600); // 10 minutes limit

// 1. Boot Laravel Application
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

use App\Models\Listing;
use Illuminate\Support\Facades\DB;

try {
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
} catch (\Exception $e) {
    die("Ошибка загрузки ядра Laravel: " . $e->getMessage());
}

$isCli = (php_sapi_name() === 'cli');

// 2. Fetch all listings
$listings = Listing::all();
$updatedCount = 0;
$fixedPathsCount = 0;
$convertedCount = 0;
$recompressedCount = 0;

echo "<h1>Восстановление и сжатие путей к изображениям (640px, 60% качество)</h1>";
echo "<p>Всего объявлений в базе: " . count($listings) . "</p>";
echo "<hr>";

// Helper function to resolve physical path on disk
function getDiskPath($webPath) {
    if (empty($webPath)) return null;
    $filename = basename($webPath);
    $possiblePaths = [
        storage_path('app/public/listings/' . $filename),
        public_path('storage/listings/' . $filename),
        public_path($webPath)
    ];
    foreach ($possiblePaths as $path) {
        if (file_exists($path) && is_file($path)) {
            return $path;
        }
    }
    return null;
}

// Helper function to convert a file to WebP
function convertToWebp($sourceFile) {
    $originalSize = filesize($sourceFile);
    $imgData = @file_get_contents($sourceFile);
    if (!$imgData) return null;

    $srcImage = @imagecreatefromstring($imgData);
    if (!$srcImage) return null;

    $width = imagesx($srcImage);
    $height = imagesy($srcImage);
    if ($width > 640) {
        $newWidth = 640;
        $newHeight = intval(($height / $width) * 640);
        $dstImage = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($dstImage, false);
        imagesavealpha($dstImage, true);
        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($srcImage);
        $srcImage = $dstImage;
    }

    $dir = dirname($sourceFile);
    $filename = basename($sourceFile);
    $filenameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
    $newFilename = $filenameWithoutExt . '_' . uniqid() . '.webp';
    $destinationFile = $dir . '/' . $newFilename;

    if (imagewebp($srcImage, $destinationFile, 60)) {
        imagedestroy($srcImage);
        @unlink($sourceFile); // Delete original
        return $newFilename;
    }

    imagedestroy($srcImage);
    return null;
}

// Helper function to compress existing WebP images to 60% quality and max 640px width
function compressExistingWebp($diskPath) {
    if (!$diskPath || !file_exists($diskPath)) return false;
    
    $srcImage = @imagecreatefromwebp($diskPath);
    if (!$srcImage) return false;
    
    $width = imagesx($srcImage);
    $height = imagesy($srcImage);
    $oldSize = filesize($diskPath);
    
    // Resize if larger than 640px
    if ($width > 640) {
        $newWidth = 640;
        $newHeight = intval(($height / $width) * 640);
        $dstImage = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($dstImage, false);
        imagesavealpha($dstImage, true);
        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($srcImage);
        $srcImage = $dstImage;
    }
    
    $tempFile = $diskPath . '.tmp';
    if (imagewebp($srcImage, $tempFile, 60)) {
        imagedestroy($srcImage);
        $newSize = filesize($tempFile);
        
        // Only replace if the new file is actually smaller
        if ($newSize < $oldSize) {
            rename($tempFile, $diskPath);
            return true;
        } else {
            @unlink($tempFile);
        }
    } else {
        imagedestroy($srcImage);
        if (file_exists($tempFile)) @unlink($tempFile);
    }
    return false;
}

// Helper to look for an already converted WebP file on disk
function findConvertedWebp($webPath) {
    $filename = basename($webPath);
    $filenameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
    
    // Directory to scan
    $dirsToScan = [
        storage_path('app/public/listings'),
        public_path('storage/listings')
    ];

    foreach ($dirsToScan as $dir) {
        if (file_exists($dir) && is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                // If it's a webp file and starts with the original filename prefix
                if (str_ends_with(strtolower($file), '.webp') && str_starts_with($file, $filenameWithoutExt)) {
                    return $file;
                }
            }
        }
    }
    return null;
}

foreach ($listings as $listing) {
    $listingChanged = false;
    $photos = is_array($listing->photos) ? $listing->photos : [];
    $updatedPhotos = [];

    // 1. Process cover image
    if (!empty($listing->image)) {
        $currentImage = $listing->image;
        if (!str_ends_with(strtolower($currentImage), '.webp')) {
            // Check if PNG/JPG exists
            $diskPath = getDiskPath($currentImage);
            if ($diskPath) {
                // PNG/JPG exists, let's convert it to WebP now
                $newFilename = convertToWebp($diskPath);
                if ($newFilename) {
                    $webDir = dirname($currentImage);
                    $newWebPath = ($webDir === '.' || $webDir === '/') ? '/storage/listings/' . $newFilename : $webDir . '/' . $newFilename;
                    $listing->image = $newWebPath;
                    $listingChanged = true;
                    $convertedCount++;
                    echo "<div style='color: #2563eb;'>✓ Сконвертировано превью для объявления #{$listing->id}: " . basename($currentImage) . " -> {$newFilename}</div>";
                }
            } else {
                // PNG/JPG does NOT exist, check if a WebP version already exists
                $webpFilename = findConvertedWebp($currentImage);
                if ($webpFilename) {
                    $webDir = dirname($currentImage);
                    $newWebPath = ($webDir === '.' || $webDir === '/') ? '/storage/listings/' . $webpFilename : $webDir . '/' . $webpFilename;
                    $listing->image = $newWebPath;
                    $listingChanged = true;
                    $fixedPathsCount++;
                    
                    // Recompress the found WebP file
                    $diskWebpPath = getDiskPath($newWebPath);
                    if ($diskWebpPath && compressExistingWebp($diskWebpPath)) {
                        $recompressedCount++;
                    }
                    
                    echo "<div style='color: #10b981;'>✓ Исправлен путь превью для объявления #{$listing->id}: " . basename($currentImage) . " -> {$webpFilename}</div>";
                } else {
                    echo "<div style='color: #d97706;'>⚠ Файл превью не найден для объявления #{$listing->id}: {$currentImage}</div>";
                }
            }
        } else {
            // If already WebP, let's recompress it
            $diskPath = getDiskPath($currentImage);
            if ($diskPath && compressExistingWebp($diskPath)) {
                $recompressedCount++;
                echo "<div style='color: #8b5cf6;'>✓ Сжато существующее WebP превью для объявления #{$listing->id}: " . basename($currentImage) . "</div>";
            }
        }
    }

    // 2. Process photos array
    foreach ($photos as $photo) {
        if (empty($photo)) continue;
        
        if (str_ends_with(strtolower($photo), '.webp')) {
            $updatedPhotos[] = $photo; // Keep WebP path
            
            // Recompress existing WebP
            $diskPath = getDiskPath($photo);
            if ($diskPath && compressExistingWebp($diskPath)) {
                $recompressedCount++;
                echo "<div style='color: #8b5cf6;'>✓ Сжато существующее WebP фото для объявления #{$listing->id}: " . basename($photo) . "</div>";
            }
            continue;
        }

        $diskPath = getDiskPath($photo);
        if ($diskPath) {
            // Convert to WebP
            $newFilename = convertToWebp($diskPath);
            if ($newFilename) {
                $webDir = dirname($photo);
                $newWebPath = ($webDir === '.' || $webDir === '/') ? '/storage/listings/' . $newFilename : $webDir . '/' . $newFilename;
                $updatedPhotos[] = $newWebPath;
                $listingChanged = true;
                $convertedCount++;
                echo "<div style='color: #2563eb;'>✓ Сконвертировано фото для объявления #{$listing->id}: " . basename($photo) . " -> {$newFilename}</div>";
            } else {
                $updatedPhotos[] = $photo;
            }
        } else {
            // Check if WebP already exists
            $webpFilename = findConvertedWebp($photo);
            if ($webpFilename) {
                $webDir = dirname($photo);
                $newWebPath = ($webDir === '.' || $webDir === '/') ? '/storage/listings/' . $webpFilename : $webDir . '/' . $webpFilename;
                $updatedPhotos[] = $newWebPath;
                $listingChanged = true;
                $fixedPathsCount++;
                
                // Recompress the found WebP file
                $diskWebpPath = getDiskPath($newWebPath);
                if ($diskWebpPath && compressExistingWebp($diskWebpPath)) {
                    $recompressedCount++;
                }
                
                echo "<div style='color: #10b981;'>✓ Исправлен путь фото для объявления #{$listing->id}: " . basename($photo) . " -> {$webpFilename}</div>";
            } else {
                $updatedPhotos[] = $photo; // Keep original path as fallback
                echo "<div style='color: #d97706;'>⚠ Фото не найдено для объявления #{$listing->id}: {$photo}</div>";
            }
        }
    }

    if ($listingChanged) {
        $listing->photos = $updatedPhotos;
        if (empty($listing->image) && count($updatedPhotos) > 0) {
            $listing->image = $updatedPhotos[0];
        }
        $listing->save();
        $updatedCount++;
    }
}

echo "<hr>";
echo "<h3>Результаты восстановления и оптимизации:</h3>";
echo "<ul>";
echo "<li>Обновлено объявлений в БД: {$updatedCount}</li>";
echo "<li>Исправлено путей к уже созданным WebP файлам: {$fixedPathsCount}</li>";
echo "<li>Новых конвертаций в WebP выполнено: {$convertedCount}</li>";
echo "<li>Существующих WebP файлов пережато (до 640px / 60% качества): {$recompressedCount}</li>";
echo "</ul>";

echo "<p style='color: #10b981; font-weight: bold;'>✓ Все изображения на сервере успешно оптимизированы под мобильные стандарты!</p>";
?>
