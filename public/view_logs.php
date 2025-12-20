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

// 添加项目日志文件（优先检查）
$projectLog = __DIR__ . '/../logs/error.log';
$projectLogDir = __DIR__ . '/../logs';

// 如果日志目录不存在，尝试创建
if (!is_dir($projectLogDir)) {
    @mkdir($projectLogDir, 0755, true);
}

// 如果日志文件不存在，尝试创建
if (!file_exists($projectLog) && is_writable($projectLogDir)) {
    @touch($projectLog);
    @chmod($projectLog, 0664);
}

// 将项目日志移到最前面（无论是否存在）
$possibleLogs = array_merge(['Project log (logs/error.log)' => $projectLog], $possibleLogs);

echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>日志位置</th><th>存在</th><th>可读</th><th>大小</th><th>最后修改</th><th>操作</th></tr>";

$foundLogs = [];

foreach ($possibleLogs as $name => $path) {
    if (empty($path) || $path === 'syslog') {
        echo "<tr><td>{$name}</td><td colspan='5' style='color:#999;'>未设置或使用系统日志</td></tr>";
        continue;
    }
    
    // 检查 open_basedir 限制
    $baseDirRestricted = false;
    $exists = false;
    $readable = false;
    $size = 0;
    $modified = 'N/A';
    
    // 检查路径是否在 open_basedir 限制内
    $openBasedir = ini_get('open_basedir');
    if (!empty($openBasedir)) {
        $allowedDirs = explode(':', $openBasedir);
        $isAllowed = false;
        foreach ($allowedDirs as $allowedDir) {
            if (empty($allowedDir)) continue;
            $allowedDir = rtrim($allowedDir, '/');
            if (strpos($path, $allowedDir) === 0) {
                $isAllowed = true;
                break;
            }
        }
        if (!$isAllowed) {
            $baseDirRestricted = true;
        }
    }
    
    // 只有在允许的路径内才检查文件是否存在
    if (!$baseDirRestricted) {
        $exists = @file_exists($path);
    }
    
    if ($exists) {
        $readable = is_readable($path);
        $size = filesize($path);
        $modified = date('Y-m-d H:i:s', filemtime($path));
        $foundLogs[] = $path;
    }
    
    $sizeFormatted = $exists ? ($size > 1024 * 1024 ? round($size / 1024 / 1024, 2) . ' MB' : round($size / 1024, 2) . ' KB') : '-';
    
    echo "<tr>";
    echo "<td><code>{$path}</code></td>";
    if ($baseDirRestricted) {
        echo "<td colspan='5' style='color:#f39c12;'>⚠️ open_basedir 限制，无法访问</td>";
    } else {
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
    }
    echo "</tr>";
}

echo "</table>";

// 2. 查看或下载日志
if (isset($_GET['view']) || isset($_GET['download'])) {
    $logName = $_GET['view'] ?? $_GET['download'];
    
    // 支持自定义路径
    if ($logName === 'custom' && isset($_GET['path'])) {
        $logPath = $_GET['path'];
    } else {
        $logPath = $possibleLogs[$logName] ?? null;
    }
    
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
        echo "<li>日志文件在其他位置（受 open_basedir 限制）</li>";
        echo "<li>PHP 配置使用系统日志（syslog）</li>";
        echo "<li>日志文件权限问题</li>";
        echo "</ul>";
        
        // 检查 open_basedir 限制
        $openBasedir = ini_get('open_basedir');
        if (!empty($openBasedir)) {
            echo "<div style='background:#fff3cd; border:1px solid #ffc107; padding:15px; margin:10px 0; border-radius:4px;'>";
            echo "<h4 style='margin-top:0; color:#856404;'>⚠️ open_basedir 限制</h4>";
            echo "<p style='color:#856404;'><strong>当前限制路径：</strong> <code>{$openBasedir}</code></p>";
            echo "<p style='color:#856404;'>由于 open_basedir 限制，脚本无法访问系统日志目录（如 /var/log）。</p>";
            echo "<p style='color:#856404;'><strong>解决方案：</strong></p>";
            echo "<ol style='color:#856404;'>";
            echo "<li><strong>在服务器上通过 SSH 查看日志：</strong><br>";
            echo "<code style='background:#f8f9fa; padding:5px; border-radius:3px;'>tail -n 100 /var/log/php-fpm/error.log</code><br>";
            echo "或<br>";
            echo "<code style='background:#f8f9fa; padding:5px; border-radius:3px;'>journalctl -u php-fpm -n 100 --no-pager</code></li>";
            echo "<li><strong>查找 PHP 错误日志位置：</strong><br>";
            echo "<code style='background:#f8f9fa; padding:5px; border-radius:3px;'>php -i | grep error_log</code></li>";
            echo "<li><strong>在项目目录创建日志文件：</strong><br>";
            echo "在项目根目录创建 <code>logs/error.log</code> 文件，并在应用启动时设置：<br>";
            echo "<code style='background:#f8f9fa; padding:5px; border-radius:3px;'>ini_set('error_log', __DIR__ . '/logs/error.log');</code></li>";
            echo "</ol>";
            echo "</div>";
            
            // 尝试在允许的目录中查找日志
            $allowedDirs = explode(':', $openBasedir);
            $projectLogs = [];
            foreach ($allowedDirs as $dir) {
                if (empty($dir)) continue;
                $dir = rtrim($dir, '/');
                $logFile = $dir . '/logs/error.log';
                if (@file_exists($logFile)) {
                    $projectLogs[] = $logFile;
                }
                $logFile2 = $dir . '/error.log';
                if (@file_exists($logFile2) && $logFile2 !== $logFile) {
                    $projectLogs[] = $logFile2;
                }
                // 检查项目目录下的 logs 目录
                $projectLogsDir = $dir . '/logs';
                if (@is_dir($projectLogsDir)) {
                    $files = @scandir($projectLogsDir);
                    if ($files) {
                        foreach ($files as $file) {
                            if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'log') {
                                $projectLogs[] = $projectLogsDir . '/' . $file;
                            }
                        }
                    }
                }
            }
            
            if (!empty($projectLogs)) {
                echo "<h4>在允许目录中找到的日志文件：</h4>";
                echo "<ul>";
                foreach ($projectLogs as $log) {
                    $logName = basename($log);
                    echo "<li><code>{$log}</code> - <a href='?view=custom&path=" . urlencode($log) . "'>查看</a> | <a href='?download=custom&path=" . urlencode($log) . "'>下载</a></li>";
                }
                echo "</ul>";
            }
        }
        
        echo "<p><strong>查找日志的方法：</strong></p>";
        echo "<pre style='background:#f5f5f5; padding:10px; border-radius:4px;'>";
        echo "# 在服务器上执行：\n";
        echo "php -i | grep error_log\n";
        echo "# 或\n";
        echo "grep -r 'error_log' /etc/php*/\n";
        echo "# 或查看 PHP-FPM 日志\n";
        echo "tail -f /var/log/php-fpm/error.log\n";
        echo "# 或查看系统日志（如果使用 syslog）\n";
        echo "journalctl -u php-fpm -n 100 --no-pager\n";
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

// 测试日志写入
if (isset($_GET['test_log'])) {
    $testLogFile = __DIR__ . '/../logs/error.log';
    $testLogDir = __DIR__ . '/../logs';
    
    // 获取 PHP 运行用户信息
    $phpUser = function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] : get_current_user();
    
    // 尝试创建目录
    $dirCreated = false;
    if (!is_dir($testLogDir)) {
        $dirCreated = @mkdir($testLogDir, 0777, true);
        if ($dirCreated) {
            @chmod($testLogDir, 0777);
        }
    } else {
        $dirCreated = true;
        // 尝试修改目录权限
        @chmod($testLogDir, 0777);
    }
    
    // 尝试创建文件
    $fileCreated = false;
    if (!file_exists($testLogFile)) {
        $fileCreated = @touch($testLogFile);
        if ($fileCreated) {
            @chmod($testLogFile, 0666);
        }
    } else {
        $fileCreated = true;
        // 尝试修改文件权限
        @chmod($testLogFile, 0666);
    }
    
    // 检查权限
    $dirWritable = is_dir($testLogDir) && is_writable($testLogDir);
    $fileWritable = file_exists($testLogFile) && is_writable($testLogFile);
    
    // 获取权限信息
    $dirPerms = is_dir($testLogDir) ? substr(sprintf('%o', fileperms($testLogDir)), -4) : 'N/A';
    $filePerms = file_exists($testLogFile) ? substr(sprintf('%o', fileperms($testLogFile)), -4) : 'N/A';
    
    $testMessage = "=== Test log entry at " . date('Y-m-d H:i:s') . " ===\n";
    if ($fileWritable) {
        $writeResult = @file_put_contents($testLogFile, $testMessage, FILE_APPEND);
        if ($writeResult !== false) {
            echo "<div style='background:#d4edda; border:1px solid #28a745; padding:15px; margin:10px 0; border-radius:4px;'>";
            echo "<h4 style='margin-top:0; color:#155724;'>✅ 测试日志写入成功</h4>";
            echo "<p style='color:#155724;'>已写入测试日志到: <code>{$testLogFile}</code></p>";
            echo "<p style='color:#155724;'><a href='?view=Project log (logs/error.log)'>查看日志</a></p>";
            echo "</div>";
        } else {
            echo "<div style='background:#f8d7da; border:1px solid #dc3545; padding:15px; margin:10px 0; border-radius:4px;'>";
            echo "<h4 style='margin-top:0; color:#721c24;'>❌ 测试日志写入失败</h4>";
            echo "<p style='color:#721c24;'>文件存在但无法写入: <code>{$testLogFile}</code></p>";
            echo "<p style='color:#721c24;'>文件权限: {$filePerms}</p>";
            echo "<p style='color:#721c24;'>PHP 运行用户: {$phpUser}</p>";
            echo "</div>";
        }
    } else {
        echo "<div style='background:#f8d7da; border:1px solid #dc3545; padding:15px; margin:10px 0; border-radius:4px;'>";
        echo "<h4 style='margin-top:0; color:#721c24;'>❌ 测试日志写入失败</h4>";
        echo "<p style='color:#721c24;'><strong>问题诊断：</strong></p>";
        echo "<ul style='color:#721c24;'>";
        echo "<li>日志目录: <code>{$testLogDir}</code></li>";
        echo "<li>目录存在: " . (is_dir($testLogDir) ? '✅' : '❌') . "</li>";
        echo "<li>目录可写: " . ($dirWritable ? '✅' : '❌') . "</li>";
        echo "<li>目录权限: {$dirPerms}</li>";
        echo "<li>日志文件: <code>{$testLogFile}</code></li>";
        echo "<li>文件存在: " . (file_exists($testLogFile) ? '✅' : '❌') . "</li>";
        echo "<li>文件可写: " . ($fileWritable ? '✅' : '❌') . "</li>";
        echo "<li>文件权限: {$filePerms}</li>";
        echo "<li>PHP 运行用户: {$phpUser}</li>";
        echo "</ul>";
        echo "<p style='color:#721c24;'><strong>解决方案：</strong></p>";
        echo "<ol style='color:#721c24;'>";
        echo "<li><strong>通过 SSH 修复权限（推荐）：</strong><br>";
        echo "<code style='background:#f8f9fa; padding:5px; border-radius:3px;'>chmod -R 777 " . dirname($testLogDir) . "/logs</code><br>";
        echo "或<br>";
        echo "<code style='background:#f8f9fa; padding:5px; border-radius:3px;'>chown -R {$phpUser}:{$phpUser} " . dirname($testLogDir) . "/logs</code><br>";
        echo "然后：<code style='background:#f8f9fa; padding:5px; border-radius:3px;'>chmod -R 755 " . dirname($testLogDir) . "/logs</code></li>";
        echo "<li><strong>使用文件管理器：</strong><br>";
        echo "在服务器文件管理器中，找到 <code>logs</code> 目录，设置权限为 777 或 755</li>";
        echo "<li><strong>联系服务器管理员：</strong><br>";
        echo "如果以上方法都不行，请联系服务器管理员修复权限问题</li>";
        echo "</ol>";
        echo "</div>";
    }
} else {
    echo "<div style='background:#e7f3ff; border:1px solid #0066cc; padding:15px; margin:10px 0; border-radius:4px;'>";
    echo "<h4 style='margin-top:0; color:#004085;'>💡 提示</h4>";
    echo "<p style='color:#004085;'>如果日志文件不存在，可以 <a href='?test_log=1' style='color:#0066cc;'>点击这里测试创建日志文件</a></p>";
    echo "</div>";
}

?>


