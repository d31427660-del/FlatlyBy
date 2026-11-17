<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Boot Laravel Application and Console Kernel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

try {
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    
    $output = new BufferedOutput();
    $status = $kernel->handle(
        new ArrayInput([
            'command' => 'migrate',
            '--force' => true,
        ]),
        $output
    );
    
    $outputContent = $output->fetch();

    echo "<div style='max-width: 700px; margin: 40px auto; padding: 30px; border: 1px solid #c2e299; background: #eaf3de; border-radius: 12px; font-family: system-ui, -apple-system, sans-serif;'>";
    echo "<h1 style='color: #3b6d11; margin-top: 0; margin-bottom: 12px; text-align: center;'>✓ Миграции Laravel запущены!</h1>";
    echo "<p style='color: #444; line-height: 1.6; text-align: center;'>Все системные таблицы и новые столбцы базы данных успешно обновлены через стандартный механизм миграций Laravel.</p>";
    
    echo "<div style='background: #fff; padding: 20px; border: 1px solid #d4ebd5; border-radius: 8px; font-family: monospace; font-size: 14px; margin: 20px 0; overflow-x: auto;'>";
    echo "<strong>Вывод консоли Laravel:</strong><br><br>";
    echo nl2br(htmlspecialchars($outputContent ?: "Нет новых миграций для выполнения (База данных уже в актуальном состоянии)."));
    echo "</div>";

    echo "<p style='text-align: center; font-weight: bold;'>Код статуса: " . $status . " (" . ($status === 0 ? "Успешно" : "Ошибка") . ")</p>";
    echo "<p style='color: #d9383a; font-weight: 700; text-align: center; margin-top: 25px;'>⚠️ Внимание: В целях безопасности ОБЯЗАТЕЛЬНО удалите файл <u>public/migrate.php</u> с вашего сервера после выполнения!</p>";
    echo "</div>";

} catch (\Exception $e) {
    echo "<div style='max-width: 650px; margin: 40px auto; padding: 30px; border: 1px solid #ffccd3; background: #fff0f2; border-radius: 12px; font-family: system-ui, -apple-system, sans-serif;'>";
    echo "<h1 style='color: #d9383a; margin-top: 0; margin-bottom: 12px;'>❌ Ошибка выполнения миграций</h1>";
    echo "<p style='color: #555;'>Произошла ошибка при загрузке ядра Laravel или выполнении команды:</p>";
    echo "<pre style='background: #fbfbfb; padding: 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 13px; overflow-x: auto; color:#d9383a;'>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "</div>";
}
