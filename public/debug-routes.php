<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Boot Laravel Application and Console Kernel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

try {
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    $router = $app['router'];
    $routes = $router->getRoutes();
    
    echo "<div style='max-width: 800px; margin: 40px auto; padding: 30px; border: 1px solid #c2d2e2; background: #f0f4f8; border-radius: 12px; font-family: system-ui, -apple-system, sans-serif;'>";
    echo "<h1 style='color: #1e3a8a; margin-top: 0; margin-bottom: 20px; text-align: center;'>Список зарегистрированных маршрутов</h1>";
    
    echo "<table style='width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);'>";
    echo "<thead style='background: #e2e8f0; text-align: left;'>";
    echo "<tr>";
    echo "<th style='padding: 12px 15px; border-bottom: 2px solid #cbd5e1;'>Методы</th>";
    echo "<th style='padding: 12px 15px; border-bottom: 2px solid #cbd5e1;'>URI</th>";
    echo "<th style='padding: 12px 15px; border-bottom: 2px solid #cbd5e1;'>Имя маршрута</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    
    $foundGoogle = false;
    foreach ($routes as $route) {
        $methods = implode('|', $route->methods());
        $uri = $route->uri();
        $name = $route->getName() ?: '-';
        
        $isGoogle = str_contains($uri, 'google');
        $highlight = $isGoogle ? "background: #fef08a; font-weight: bold; color: #854d0e;" : "";
        if ($isGoogle) $foundGoogle = true;
        
        echo "<tr style='border-bottom: 1px solid #e2e8f0; {$highlight}'>";
        echo "<td style='padding: 10px 15px; font-family: monospace; font-size: 13px;'>{$methods}</td>";
        echo "<td style='padding: 10px 15px; font-family: monospace; font-size: 13px;'>/{$uri}</td>";
        echo "<td style='padding: 10px 15px; font-family: monospace; font-size: 13px;'>{$name}</td>";
        echo "</tr>";
    }
    
    echo "</tbody>";
    echo "</table>";
    
    if (!$foundGoogle) {
        echo "<div style='margin-top: 20px; padding: 15px; background: #fee2e2; border: 1px solid #fecaca; border-radius: 8px; color: #991b1b; font-weight: bold; text-align: center;'>";
        echo "⚠️ Маршруты для Google OAuth НЕ найдены в таблице маршрутизации Laravel!";
        echo "</div>";
    } else {
        echo "<div style='margin-top: 20px; padding: 15px; background: #dcfce7; border: 1px solid #bbf7d0; border-radius: 8px; color: #166534; font-weight: bold; text-align: center;'>";
        echo "✓ Маршруты для Google OAuth присутствуют в таблице маршрутизации!";
        echo "</div>";
    }

    echo "<p style='color: #d9383a; font-weight: 700; text-align: center; margin-top: 25px;'>⚠️ Внимание: ОБЯЗАТЕЛЬНО удалите файл <u>public/debug-routes.php</u> с вашего сервера после проверки!</p>";
    echo "</div>";
} catch (\Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}
