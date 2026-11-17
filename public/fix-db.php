<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Read .env file parameters
$envPath = __DIR__ . '/../.env';
if (!file_exists($envPath)) {
    die("Файл .env не найден.");
}

$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$config = [];
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    $parts = explode('=', $line, 2);
    if (count($parts) === 2) {
        $config[trim($parts[0])] = trim($parts[1], " '\"");
    }
}

$dbHost = $config['DB_HOST'] ?? '127.0.0.1';
$dbPort = $config['DB_PORT'] ?? '3306';
$dbName = $config['DB_DATABASE'] ?? '';
$dbUser = $config['DB_USERNAME'] ?? '';
$dbPass = $config['DB_PASSWORD'] ?? '';

// Helper function to check if a table exists
function tableExists($pdo, $table) {
    $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
    $stmt->execute([$table]);
    return $stmt->rowCount() > 0;
}

// Helper function to check if a column exists
function columnExists($pdo, $table, $column) {
    $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
    $stmt->execute([$column]);
    return $stmt->rowCount() > 0;
}

try {
    // 2. Connect via PDO
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $log = [];

    // --- USERS TABLE ---
    if (tableExists($pdo, 'users')) {
        $log[] = "Таблица <code>users</code> проверена:";
        
        // Add telegram
        if (!columnExists($pdo, 'users', 'telegram')) {
            $pdo->exec("ALTER TABLE `users` ADD `telegram` VARCHAR(255) NULL AFTER `phone`");
            $log[] = " — <span style='color:green;'>✓</span> Добавлена отсутствующая колонка <code>telegram</code>";
        } else {
            $log[] = " — Колонка <code>telegram</code> уже присутствует";
        }

        // Add viber
        if (!columnExists($pdo, 'users', 'viber')) {
            $pdo->exec("ALTER TABLE `users` ADD `viber` VARCHAR(255) NULL AFTER `telegram`");
            $log[] = " — <span style='color:green;'>✓</span> Добавлена отсутствующая колонка <code>viber</code>";
        } else {
            $log[] = " — Колонка <code>viber</code> уже присутствует";
        }

        // Add unp
        if (!columnExists($pdo, 'users', 'unp')) {
            $pdo->exec("ALTER TABLE `users` ADD `unp` VARCHAR(50) NULL AFTER `viber`");
            $log[] = " — <span style='color:green;'>✓</span> Добавлена отсутствующая колонка <code>unp</code>";
        } else {
            $log[] = " — Колонка <code>unp</code> уже присутствует";
        }

        // Add avatar
        if (!columnExists($pdo, 'users', 'avatar')) {
            $pdo->exec("ALTER TABLE `users` ADD `avatar` LONGTEXT NULL AFTER `unp`");
            $log[] = " — <span style='color:green;'>✓</span> Добавлена отсутствующая колонка <code>avatar</code>";
        } else {
            $log[] = " — Колонка <code>avatar</code> уже присутствует";
        }
    } else {
        $log[] = "<strong style='color:red;'>❌ Ошибка:</strong> Таблица <code>users</code> не найдена!";
    }

    // --- LISTINGS TABLE ---
    if (tableExists($pdo, 'listings')) {
        $log[] = "Таблица <code>listings</code> проверена:";

        // Alter status column
        $pdo->exec("ALTER TABLE `listings` MODIFY `status` VARCHAR(50) NOT NULL DEFAULT 'pending'");
        $log[] = " — <span style='color:green;'>✓</span> Тип колонки <code>status</code> обновлен до VARCHAR(50) (со значением 'pending' по умолчанию)";

        // Alter unp column
        $pdo->exec("ALTER TABLE `listings` MODIFY `unp` VARCHAR(50) NULL");
        $log[] = " — <span style='color:green;'>✓</span> Колонка <code>unp</code> обновлена (разрешены значения NULL)";

        // Add image
        if (!columnExists($pdo, 'listings', 'image')) {
            $pdo->exec("ALTER TABLE `listings` ADD `image` VARCHAR(255) NULL AFTER `status`");
            $log[] = " — <span style='color:green;'>✓</span> Добавлена отсутствующая колонка <code>image</code>";
        } else {
            $log[] = " — Колонка <code>image</code> уже присутствует";
        }

        // Add moderation_reason
        if (!columnExists($pdo, 'listings', 'moderation_reason')) {
            $pdo->exec("ALTER TABLE `listings` ADD `moderation_reason` TEXT NULL AFTER `status`");
            $log[] = " — <span style='color:green;'>✓</span> Добавлена отсутствующая колонка <code>moderation_reason</code>";
        } else {
            $log[] = " — Колонка <code>moderation_reason</code> уже присутствует";
        }

        // Add phone
        if (!columnExists($pdo, 'listings', 'phone')) {
            $pdo->exec("ALTER TABLE `listings` ADD `phone` VARCHAR(255) NULL AFTER `description`");
            $log[] = " — <span style='color:green;'>✓</span> Добавлена отсутствующая колонка <code>phone</code>";
        } else {
            $log[] = " — Колонка <code>phone</code> уже присутствует";
        }
    } else {
        $log[] = "<strong style='color:red;'>❌ Ошибка:</strong> Таблица <code>listings</code> не найдена!";
    }

    // --- SESSIONS TABLE ---
    if (!tableExists($pdo, 'sessions')) {
        $pdo->exec("CREATE TABLE `sessions` (
            `id` VARCHAR(255) NOT NULL,
            `user_id` BIGINT(20) UNSIGNED NULL,
            `ip_address` VARCHAR(45) NULL,
            `user_agent` TEXT NULL,
            `payload` LONGTEXT NOT NULL,
            `last_activity` INT(11) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `sessions_user_id_index` (`user_id`),
            KEY `sessions_last_activity_index` (`last_activity`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $log[] = "Таблица сессий: <span style='color:green;'>✓ Создана новая таблица `sessions`</span>";
    } else {
        $log[] = "Таблица сессий: <code>sessions</code> уже присутствует";
    }

    // --- CACHE TABLES ---
    if (!tableExists($pdo, 'cache')) {
        $pdo->exec("CREATE TABLE `cache` (
            `key` VARCHAR(255) NOT NULL,
            `value` MEDIUMTEXT NOT NULL,
            `expiration` BIGINT(20) NOT NULL,
            PRIMARY KEY (`key`),
            KEY `cache_expiration_index` (`expiration`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $log[] = "Таблица кэша: <span style='color:green;'>✓ Создана новая таблица `cache`</span>";
    } else {
        $log[] = "Таблица кэша: <code>cache</code> уже присутствует";
    }

    if (!tableExists($pdo, 'cache_locks')) {
        $pdo->exec("CREATE TABLE `cache_locks` (
            `key` VARCHAR(255) NOT NULL,
            `owner` VARCHAR(255) NOT NULL,
            `expiration` BIGINT(20) NOT NULL,
            PRIMARY KEY (`key`),
            KEY `cache_locks_expiration_index` (`expiration`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $log[] = "Таблица блокировок кэша: <span style='color:green;'>✓ Создана новая таблица `cache_locks`</span>";
    } else {
        $log[] = "Таблица блокировок кэша: <code>cache_locks</code> уже присутствует";
    }

    // --- JOBS TABLES ---
    if (!tableExists($pdo, 'jobs')) {
        $pdo->exec("CREATE TABLE `jobs` (
            `id` BIGINT(20) UNSIGNED AUTO_INCREMENT NOT NULL,
            `queue` VARCHAR(255) NOT NULL,
            `payload` LONGTEXT NOT NULL,
            `attempts` TINYINT(3) UNSIGNED NOT NULL,
            `reserved_at` INT(10) UNSIGNED NULL,
            `available_at` INT(10) UNSIGNED NOT NULL,
            `created_at` INT(10) UNSIGNED NOT NULL,
            PRIMARY KEY (`id`),
            KEY `jobs_queue_index` (`queue`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $log[] = "Таблица задач: <span style='color:green;'>✓ Создана новая таблица `jobs`</span>";
    } else {
        $log[] = "Таблица задач: <code>jobs</code> уже присутствует";
    }

    if (!tableExists($pdo, 'job_batches')) {
        $pdo->exec("CREATE TABLE `job_batches` (
            `id` VARCHAR(255) NOT NULL,
            `name` VARCHAR(255) NOT NULL,
            `total_jobs` INT(11) NOT NULL,
            `pending_jobs` INT(11) NOT NULL,
            `failed_jobs` INT(11) NOT NULL,
            `failed_job_ids` LONGTEXT NOT NULL,
            `options` MEDIUMTEXT NULL,
            `cancelled_at` INT(11) NULL,
            `created_at` INT(11) NOT NULL,
            `finished_at` INT(11) NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $log[] = "Таблица пакетов задач: <span style='color:green;'>✓ Создана новая таблица `job_batches`</span>";
    } else {
        $log[] = "Таблица пакетов задач: <code>job_batches</code> уже присутствует";
    }

    if (!tableExists($pdo, 'failed_jobs')) {
        $pdo->exec("CREATE TABLE `failed_jobs` (
            `id` BIGINT(20) UNSIGNED AUTO_INCREMENT NOT NULL,
            `uuid` VARCHAR(255) NOT NULL,
            `connection` TEXT NOT NULL,
            `queue` TEXT NOT NULL,
            `payload` LONGTEXT NOT NULL,
            `exception` LONGTEXT NOT NULL,
            `failed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $log[] = "Таблица неудавшихся задач: <span style='color:green;'>✓ Создана новая таблица `failed_jobs`</span>";
    } else {
        $log[] = "Таблица неудавшихся задач: <code>failed_jobs</code> уже присутствует";
    }

    echo "<div style='max-width: 700px; margin: 40px auto; padding: 30px; border: 1px solid #c2e299; background: #eaf3de; border-radius: 12px; font-family: system-ui, -apple-system, sans-serif;'>";
    echo "<h1 style='color: #3b6d11; margin-top: 0; margin-bottom: 12px; text-align: center;'>✓ База данных успешно синхронизирована!</h1>";
    echo "<p style='color: #444; line-height: 1.6; text-align: center;'>Все расхождения между структурой кода и импортированной базой данных MySQL полностью устранены.</p>";
    
    echo "<div style='background: #fff; padding: 20px; border: 1px solid #d4ebd5; border-radius: 8px; font-size: 14px; margin: 20px 0;'>";
    echo "<h3 style='margin-top:0; color:#2d5012;'>Детали синхронизации:</h3>";
    echo "<ul style='padding-left: 20px; line-height: 1.8; color: #333; margin-bottom: 0;'>";
    foreach ($log as $item) {
        echo "<li>$item</li>";
    }
    echo "</ul>";
    echo "</div>";

    echo "<p style='color: #d9383a; font-weight: 700; text-align: center; margin-top: 25px;'>⚠️ Внимание: В целях безопасности ОБЯЗАТЕЛЬНО удалите файл <u>public/fix-db.php</u> с вашего сервера после выполнения!</p>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<div style='max-width: 650px; margin: 40px auto; padding: 30px; border: 1px solid #ffccd3; background: #fff0f2; border-radius: 12px; font-family: system-ui, -apple-system, sans-serif;'>";
    echo "<h1 style='color: #d9383a; margin-top: 0; margin-bottom: 12px;'>❌ Ошибка обновления структуры БД</h1>";
    echo "<p style='color: #555;'>Не удалось применить изменения структуры. Ошибка подключения к СУБД или выполнения SQL-запроса:</p>";
    echo "<pre style='background: #fbfbfb; padding: 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 13px; overflow-x: auto; color:#d9383a;'>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "</div>";
}
