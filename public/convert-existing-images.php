<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(180); // Increase time limit to 3 minutes per batch

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

// 2. Check GD library features
$gdInfo = function_exists('gd_info') ? gd_info() : [];
$webpSupported = !empty($gdInfo['WebP Support']);

if (!$webpSupported) {
    echo "<div style='max-width: 650px; margin: 40px auto; padding: 30px; border: 1px solid #ffccd3; background: #fff0f2; border-radius: 12px; font-family: system-ui, -apple-system, sans-serif;'>";
    echo "<h1 style='color: #d9383a; margin-top: 0; margin-bottom: 12px;'>❌ GD-библиотека не поддерживает WebP</h1>";
    echo "<p style='color: #555;'>Для конвертации изображений на вашем сервере должно быть установлено расширение PHP GD с поддержкой WebP. Обратитесь к вашему хостинг-провайдеру для его включения.</p>";
    echo "</div>";
    exit;
}

// 3. Batch processing parameters
$isCli = (php_sapi_name() === 'cli');
$batchSize = $isCli ? 999999 : 15;
$offset = ($isCli || !isset($_GET['offset'])) ? 0 : intval($_GET['offset']);

// Query listings that have non-webp main image or contain PNG/JPG images in photos array
$query = Listing::where(function($q) {
    $q->where('image', 'NOT LIKE', '%.webp')
      ->where('image', '!=', '')
      ->whereNotNull('image');
})->orWhere(function($q) {
    $q->where('photos', 'LIKE', '%.jpg%')
      ->orWhere('photos', 'LIKE', '%.jpeg%')
      ->orWhere('photos', 'LIKE', '%.png%');
});

$totalListings = $query->count();
$listings = $query->skip($offset)->take($batchSize)->get();

$processedCount = 0;
$convertedImages = [];
$totalBytesSaved = 0;

foreach ($listings as $listing) {
    $listingChanged = false;
    $photos = is_array($listing->photos) ? $listing->photos : [];
    $updatedPhotos = [];

    // Helper closure to process single image path
    $processImagePath = function($webPath) use (&$convertedImages, &$totalBytesSaved) {
        if (empty($webPath) || str_ends_with(strtolower($webPath), '.webp')) {
            return $webPath; // Already webp or empty
        }

        $filename = basename($webPath);
        
        // Search in possible directories
        $possiblePaths = [
            storage_path('app/public/listings/' . $filename),
            public_path('storage/listings/' . $filename),
            public_path($webPath)
        ];

        $sourceFile = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path) && is_file($path)) {
                $sourceFile = $path;
                break;
            }
        }

        if (!$sourceFile) {
            return $webPath; // File not found on disk, keep original path
        }

        $originalSize = filesize($sourceFile);
        $imgData = @file_get_contents($sourceFile);
        if (!$imgData) {
            return $webPath;
        }

        $srcImage = @imagecreatefromstring($imgData);
        if (!$srcImage) {
            return $webPath; // Failed to parse image
        }

        // Resize if too large
        $width = imagesx($srcImage);
        $height = imagesy($srcImage);
        if ($width > 800) {
            $newWidth = 800;
            $newHeight = intval(($height / $width) * 800);
            $dstImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preserve transparency
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
            
            imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($srcImage);
            $srcImage = $dstImage;
        }

        $dir = dirname($sourceFile);
        $filenameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
        $newFilename = $filenameWithoutExt . '_' . uniqid() . '.webp';
        $destinationFile = $dir . '/' . $newFilename;

        // Save WebP with 78% quality
        if (imagewebp($srcImage, $destinationFile, 78)) {
            imagedestroy($srcImage);
            $newSize = filesize($destinationFile);
            $savedBytes = $originalSize - $newSize;
            $totalBytesSaved += max(0, $savedBytes);

            $convertedImages[] = [
                'original' => $filename,
                'new' => $newFilename,
                'saved_kb' => round($savedBytes / 1024, 1)
            ];

            // Delete original file to free space
            @unlink($sourceFile);

            // Return new web path
            $webDir = dirname($webPath);
            return ($webDir === '.' || $webDir === '/') ? '/storage/listings/' . $newFilename : $webDir . '/' . $newFilename;
        }

        imagedestroy($srcImage);
        return $webPath;
    };

    // Process cover image
    if (!empty($listing->image) && !str_ends_with(strtolower($listing->image), '.webp')) {
        $oldImage = $listing->image;
        $newImage = $processImagePath($oldImage);
        if ($newImage !== $oldImage) {
            $listing->image = $newImage;
            $listingChanged = true;
        }
    }

    // Process all photos
    foreach ($photos as $photo) {
        $newPhoto = $processImagePath($photo);
        if ($newPhoto !== $photo) {
            $listingChanged = true;
        }
        $updatedPhotos[] = $newPhoto;
    }

    if ($listingChanged) {
        $listing->photos = $updatedPhotos;
        // Make sure image field has a valid first photo if it gets lost
        if (empty($listing->image) && count($updatedPhotos) > 0) {
            $listing->image = $updatedPhotos[0];
        }
        $listing->save();
        $processedCount++;
    }
}

// Accumulate total saved bytes across steps using session or database or simply log
session_start();
$_SESSION['total_bytes_saved'] = ($_SESSION['total_bytes_saved'] ?? 0) + $totalBytesSaved;
$_SESSION['converted_count'] = ($_SESSION['converted_count'] ?? 0) + count($convertedImages);

$nextOffset = $offset + $batchSize;
$isDone = ($nextOffset >= $totalListings || count($listings) === 0);

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оптимизация изображений каталога</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background-color: #f4f6f8;
            color: #333;
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            max-width: 700px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }
        h1 {
            color: #1a1a1a;
            font-size: 24px;
            margin-top: 0;
        }
        .progress-bar-container {
            background-color: #e9ecef;
            border-radius: 8px;
            height: 16px;
            overflow: hidden;
            margin: 20px 0;
        }
        .progress-bar {
            background-color: #10b981;
            height: 100%;
            transition: width 0.3s ease;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-card .value {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
        }
        .stat-card .label {
            font-size: 13px;
            color: #64748b;
            margin-top: 5px;
        }
        .log-box {
            background: #1e293b;
            color: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 13px;
            max-height: 200px;
            overflow-y: auto;
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background: #1d4ed8;
        }
        .alert-warning {
            background: #fffbeb;
            border: 1px solid #fef3c7;
            color: #92400e;
            padding: 15px;
            border-radius: 8px;
            margin-top: 25px;
            font-size: 14px;
        }
    </style>
    <?php if (!$isDone): ?>
    <script>
        setTimeout(function() {
            window.location.href = "?offset=<?= $nextOffset ?>";
        }, 1200);
    </script>
    <?php endif; ?>
</head>
<body>
<div class="container">
    <?php if ($isDone): ?>
        <h1 style="color: #10b981; text-align: center;">✓ Оптимизация завершена!</h1>
        <p style="text-align: center; color: #475569;">Все существующие изображения объявлений успешно пережаты в формат WebP и уменьшены.</p>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="value"><?= $_SESSION['converted_count'] ?? 0 ?></div>
                <div class="label">Обработано изображений</div>
            </div>
            <div class="stat-card">
                <div class="value"><?= round(($_SESSION['total_bytes_saved'] ?? 0) / 1024 / 1024, 2) ?> МБ</div>
                <div class="label">Сэкономлено места на диске</div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="/" class="btn">Вернуться на сайт</a>
        </div>

        <div class="alert-warning">
            <strong>⚠️ Внимание:</strong> В целях безопасности обязательно удалите этот файл <code>public/convert-existing-images.php</code> с вашего сервера после выполнения!
        </div>
        
        <?php
        // Reset stats
        unset($_SESSION['total_bytes_saved']);
        unset($_SESSION['converted_count']);
        ?>

    <?php else: ?>
        <h1>Оптимизация изображений...</h1>
        <p style="color: #475569;">Обработка объявлений <?= $offset ?> - <?= min($offset + $batchSize, $totalListings) ?> из <?= $totalListings ?>.</p>
        
        <div class="progress-bar-container">
            <div class="progress-bar" style="width: <?= round(($offset / $totalListings) * 100) ?>%;"></div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="value"><?= $_SESSION['converted_count'] ?? 0 ?></div>
                <div class="label">Обработано изображений</div>
            </div>
            <div class="stat-card">
                <div class="value"><?= round(($_SESSION['total_bytes_saved'] ?? 0) / 1024 / 1024, 2) ?> МБ</div>
                <div class="label">Сэкономлено места</div>
            </div>
        </div>

        <div class="log-box">
            <?php if (empty($convertedImages)): ?>
                <div>В этой пачке нет изображений, требующих конвертации. Переход к следующей...</div>
            <?php else: ?>
                <?php foreach ($convertedImages as $img): ?>
                    <div style="color: #34d399;">✓ <?= htmlspecialchars($img['original']) ?> -> WebP (сжато на <?= $img['saved_kb'] ?> КБ)</div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <p style="font-size: 13px; color: #94a3b8; animation: pulse 1.5s infinite;">Страница автоматически перезагрузится для обработки следующей партии...</p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
