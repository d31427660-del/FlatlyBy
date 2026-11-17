<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Boot Laravel Application and Console Kernel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

$commands = [
    'route:clear'  => 'Очистка кэша маршрутов (Routes Cache)',
    'config:clear' => 'Очистка кэша конфигурации (Config Cache)',
    'cache:clear'  => 'Очистка общего кэша приложения (Application Cache)',
    'view:clear'   => 'Очистка кэша представлений Blade (Compiled Views Cache)',
    'storage:link' => 'Создание символической ссылки хранилища (Storage Link)',
];

try {
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    
    echo "<div style='max-width: 700px; margin: 40px auto; padding: 30px; border: 1px solid #c2e299; background: #eaf3de; border-radius: 12px; font-family: system-ui, -apple-system, sans-serif;'>";
    echo "<h1 style='color: #3b6d11; margin-top: 0; margin-bottom: 20px; text-align: center;'>✓ Запуск очистки кэша Laravel</h1>";
    
    foreach ($commands as $cmd => $desc) {
        $output = new BufferedOutput();
        $status = $kernel->handle(new ArrayInput(['command' => $cmd]), $output);
        $outputContent = $output->fetch();
        
        echo "<div style='background: #fff; padding: 15px; border: 1px solid #d4ebd5; border-radius: 8px; font-family: monospace; font-size: 14px; margin-bottom: 15px;'>";
        echo "<strong>{$desc} (команда: {$cmd}):</strong><br>";
        echo "<span style='color: " . ($status === 0 ? "green" : "red") . "; font-weight: bold;'>Статус: " . ($status === 0 ? "Успешно" : "Ошибка (код {$status})") . "</span><br>";
        echo "<pre style='margin: 10px 0 0 0; background: #f9f9f9; padding: 10px; border: 1px solid #eee; border-radius: 4px; overflow-x: auto; white-space: pre-wrap;'>" . htmlspecialchars(trim($outputContent ?: "Вывод пуст")) . "</pre>";
        echo "</div>";
    }

    echo "<p style='color: #d9383a; font-weight: 700; text-align: center; margin-top: 25px;'>⚠️ Внимание: В целях безопасности ОБЯЗАТЕЛЬНО удалите файл <u>public/clear-cache.php</u> с вашего сервера после выполнения!</p>";
    echo "</div>";

} catch (\Exception $e) {
    echo "<div style='max-width: 650px; margin: 40px auto; padding: 30px; border: 1px solid #ffccd3; background: #fff0f2; border-radius: 12px; font-family: system-ui, -apple-system, sans-serif;'>";
    echo "<h1 style='color: #d9383a; margin-top: 0; margin-bottom: 12px;'>❌ Ошибка выполнения очистки кэша</h1>";
    echo "<p style='color: #555;'>Произошла ошибка при загрузке ядра Laravel или выполнении команд:</p>";
    echo "<pre style='background: #fbfbfb; padding: 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 13px; overflow-x: auto; color:#d9383a;'>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "</div>";
}
