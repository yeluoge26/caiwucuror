<?php
/**
 * 查看错误日志脚本
 * 用于查看 PHP 错误日志，方便排查问题
 */

// 显示所有错误
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=utf-8');

echo "<h2>错误日志查看器</h2>";

// 1. 查找错误日志位置
echo "<h3>1. 错误日志位置</h3>";

$errorLog = ini_get('error_log');
$possibleLogs = [
    'PHP error_log setting' => $errorLog,
    '/var/log/php-fpm/error.log' => '/var/log/php-fpm/error.log',
    '/var/log/php_errors.log' => '/var/log/php_errors.log',
    '/var/log/apache2/error.log' => '/var/log/apache2/error.log',
    '/var/log/nginx/error.log' => '/var/log/nginx/error.log',
    __DIR__ . '/../logs/error.log' => __DIR__ . '/../logs/error.log',
    __DIR__ . '/error.log' => __DIR__ . '/error.log',
];

echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>日志位置</th><th>存在</th><th>可读</th><th>大小</th><th>最后修改</th><th>操作</th></tr>";

$foundLogs = [];

foreach ($possibleLogs as $name => $path) {
    if (empty($path) || $path === 'syslog') {
        echo "<tr><td>{$name}</td><td colspan='5' style='color:#999;'>未设置或使用系统日志</td></tr>";
        continue;
    }
    
    $exists = file_exists($path);
    $readable = $exists && is_readable($path);
    $size = $exists ? filesize($path) : 0;
    $modified = $exists ? date('Y-m-d H:i:s', filemtime($path)) : 'N/A';
    
    if ($exists) {
        $foundLogs[] = $path;
    }
    
    $sizeFormatted = $exists ? ($size > 1024 * 1024 ? round($size / 1024 / 1024, 2) . ' MB' : round($size / 1024, 2) . ' KB') : '-';
    
    echo "<tr>";
    echo "<td><code>{$path}</code></td>";
    echo "<td>" . ($exists ? '✅' : '❌') . "</td>";
    echo "<td>" . ($readable ? '✅' : '❌') . "</td>";
    echo "<td>{$sizeFormatted}</td>";
    echo "<td>{$modified}</td>";
    echo "<td>";
    if ($readable) {
        echo "<a href='?view={$name}' style='margin-right: 10px;'>查看</a>";
        echo "<a href='?download={$name}'>下载</a>";
    }
    echo "</td>";
    echo "</tr>";
}

echo "</table>";

// 2. 查看或下载日志
if (isset($_GET['view']) || isset($_GET['download'])) {
    $logName = $_GET['view'] ?? $_GET['download'];
    $logPath = $possibleLogs[$logName] ?? null;
    
    if ($logPath && file_exists($logPath) && is_readable($logPath)) {
        if (isset($_GET['download'])) {
            // 下载日志文件
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="error_log_' . date('Y-m-d_His') . '.txt"');
            header('Content-Length: ' . filesize($logPath));
            readfile($logPath);
            exit;
        } else {
            // 查看日志（显示最后 N 行）
            $lines = isset($_GET['lines']) ? (int)$_GET['lines'] : 100;
            $fileLines = file($logPath);
            $recentLines = array_slice($fileLines, -$lines);
            
            echo "<h3>2. 日志内容（最后 {$lines} 行）</h3>";
            echo "<p><a href='?view={$logName}&lines=" . ($lines + 100) . "'>查看更多（+100行）</a> | ";
            echo "<a href='?download={$logName}'>下载完整日志</a> | ";
            echo "<a href='?'>返回</a></p>";
            echo "<pre style='background:#f5f5f5; padding:15px; border:1px solid #ddd; border-radius:4px; max-height:600px; overflow:auto; font-size:12px; line-height:1.4;'>";
            echo htmlspecialchars(implode('', $recentLines));
            echo "</pre>";
        }
    } else {
        echo "<p style='color:red;'>日志文件不存在或不可读：{$logPath}</p>";
    }
} else {
    // 3. 显示最近的错误（从所有找到的日志中）
    if (!empty($foundLogs)) {
        echo "<h3>2. 最近的错误（从所有日志中）</h3>";
        echo "<p>显示所有找到的日志文件的最后 50 行：</p>";
        
        foreach ($foundLogs as $logPath) {
            if (is_readable($logPath)) {
                echo "<h4>日志：{$logPath}</h4>";
                $fileLines = file($logPath);
                $recentLines = array_slice($fileLines, -50);
                echo "<pre style='background:#f5f5f5; padding:15px; border:1px solid #ddd; border-radius:4px; max-height:300px; overflow:auto; font-size:11px; line-height:1.3;'>";
                echo htmlspecialchars(implode('', $recentLines));
                echo "</pre>";
                echo "<p><a href='?view=" . array_search($logPath, $possibleLogs) . "'>查看完整日志</a> | ";
                echo "<a href='?download=" . array_search($logPath, $possibleLogs) . "'>下载</a></p>";
                echo "<hr>";
            }
        }
    } else {
        echo "<h3>2. 未找到日志文件</h3>";
        echo "<p style='color:orange;'>未找到任何错误日志文件。可能的原因：</p>";
        echo "<ul>";
        echo "<li>日志文件在其他位置</li>";
        echo "<li>PHP 配置使用系统日志（syslog）</li>";
        echo "<li>日志文件权限问题</li>";
        echo "</ul>";
        echo "<p><strong>查找日志的方法：</strong></p>";
        echo "<pre style='background:#f5f5f5; padding:10px; border-radius:4px;'>";
        echo "# 在服务器上执行：\n";
        echo "php -i | grep error_log\n";
        echo "# 或\n";
        echo "grep -r 'error_log' /etc/php*/\n";
        echo "# 或查看 PHP-FPM 日志\n";
        echo "tail -f /var/log/php-fpm/error.log\n";
        echo "</pre>";
    }
}

// 4. 显示 PHP 配置信息
echo "<h3>3. PHP 配置信息</h3>";
echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>配置项</th><th>值</th></tr>";
echo "<tr><td>error_log</td><td><code>" . htmlspecialchars(ini_get('error_log')) . "</code></td></tr>";
echo "<tr><td>log_errors</td><td>" . (ini_get('log_errors') ? '✅ On' : '❌ Off') . "</td></tr>";
echo "<tr><td>display_errors</td><td>" . (ini_get('display_errors') ? '✅ On' : '❌ Off') . "</td></tr>";
echo "<tr><td>error_reporting</td><td><code>" . ini_get('error_reporting') . "</code></td></tr>";
echo "</table>";

// 5. 搜索特定关键词
if (isset($_GET['search']) && !empty($_GET['search'])) {
    echo "<h3>4. 搜索结果</h3>";
    $searchTerm = $_GET['search'];
    $results = [];
    
    foreach ($foundLogs as $logPath) {
        if (is_readable($logPath)) {
            $fileLines = file($logPath);
            foreach ($fileLines as $lineNum => $line) {
                if (stripos($line, $searchTerm) !== false) {
                    $results[] = [
                        'file' => $logPath,
                        'line' => $lineNum + 1,
                        'content' => $line
                    ];
                }
            }
        }
    }
    
    if (!empty($results)) {
        echo "<p>找到 " . count($results) . " 条匹配结果：</p>";
        echo "<pre style='background:#f5f5f5; padding:15px; border:1px solid #ddd; border-radius:4px; max-height:400px; overflow:auto; font-size:11px;'>";
        foreach ($results as $result) {
            echo "[" . basename($result['file']) . ":" . $result['line'] . "] " . htmlspecialchars($result['content']);
        }
        echo "</pre>";
    } else {
        echo "<p style='color:orange;'>未找到包含 '{$searchTerm}' 的日志条目。</p>";
    }
}

// 搜索表单
echo "<h3>5. 搜索日志</h3>";
echo "<form method='get' style='margin-bottom:20px;'>";
echo "<input type='text' name='search' placeholder='输入搜索关键词（如：inspection, photo, upload）' style='padding:8px; width:400px; border:1px solid #ddd; border-radius:4px;'> ";
echo "<button type='submit' style='padding:8px 15px; background:#3498db; color:white; border:none; border-radius:4px; cursor:pointer;'>搜索</button>";
echo "</form>";

?>

