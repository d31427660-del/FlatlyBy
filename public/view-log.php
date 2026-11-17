<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$logPath = __DIR__ . '/../storage/logs/laravel.log';

echo "<div style='max-width: 950px; margin: 40px auto; padding: 30px; border: 1px solid #fecaca; background: #fef2f2; border-radius: 12px; font-family: system-ui, -apple-system, sans-serif;'>";
echo "<h1 style='color: #991b1b; margin-top: 0; margin-bottom: 20px; text-align: center;'>Лог ошибок Laravel на сервере</h1>";

if (!file_exists($logPath)) {
    echo "<p style='color: #555; text-align: center;'>Файл логов <code>laravel.log</code> не найден по пути: <code>" . htmlspecialchars($logPath) . "</code></p>";
} else {
    $lines = file($logPath);
    // filter empty lines and slice last 80 lines
    $lines = array_filter($lines);
    $lastLines = array_slice($lines, -80);
    
    echo "<pre style='background: #ffffff; padding: 20px; border: 1px solid #e5e7eb; border-radius: 8px; font-family: monospace; font-size: 13px; overflow-x: auto; max-height: 600px; white-space: pre-wrap; color: #1f2937;'>";
    foreach ($lastLines as $line) {
        echo htmlspecialchars($line);
    }
    echo "</pre>";
}

echo "<p style='color: #d9383a; font-weight: 700; text-align: center; margin-top: 25px;'>⚠️ Внимание: ОБЯЗАТЕЛЬНО удалите файл <u>public/view-log.php</u> с вашего сервера после проверки!</p>";
echo "</div>";
