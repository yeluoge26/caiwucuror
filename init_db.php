<?php
/**
 * 数据库初始化脚本（增强版）
 * 使用方法: php init_db.php
 * 或在浏览器访问: http://localhost/init_db.php
 * 
 * 功能：
 * - 自动创建数据库（如果不存在）
 * - 执行完整的schema.sql
 * - 检查所有表是否创建成功
 * - 显示初始化结果
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>数据库初始化</title>";
echo "<style>
body{font-family:Arial,sans-serif;padding:20px;max-width:1000px;margin:0 auto;background:#f5f5f5;}
.ok{color:green;font-weight:bold;}
.error{color:red;font-weight:bold;}
.warning{color:orange;font-weight:bold;}
.section{margin:20px 0;padding:20px;border:1px solid #ddd;background:white;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);}
h1{color:#333;border-bottom:3px solid #3498db;padding-bottom:10px;}
h2{color:#555;margin-top:0;}
code{background:#f0f0f0;padding:2px 6px;border-radius:3px;font-family:monospace;}
table{border-collapse:collapse;width:100%;margin-top:10px;}
th,td{padding:8px;text-align:left;border:1px solid #ddd;}
th{background:#3498db;color:white;}
tr:nth-child(even){background:#f9f9f9;}
button{padding:12px 24px;background:#4caf50;color:white;border:none;border-radius:4px;cursor:pointer;font-size:16px;font-weight:bold;}
button:hover{background:#45a049;}
.info{background:#e3f2fd;padding:12px;border-radius:4px;margin:10px 0;border-left:4px solid #2196f3;}
</style></head><body><h1>🗄️ 数据库初始化工具</h1>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['init'])) {
    try {
        // 加载配置
        if (!file_exists('app/config/db.php')) {
            throw new Exception("数据库配置文件不存在: app/config/db.php<br>请先复制 app/config/db.php.example 为 app/config/db.php 并配置数据库信息");
        }
        
        require_once 'app/config/db.php';
        $config = require 'app/config/db.php';
        
        echo "<div class='section'>";
        echo "<h2>📋 开始初始化...</h2>";
        echo "<div class='info'>";
        echo "<strong>数据库配置：</strong><br>";
        echo "主机: <code>" . htmlspecialchars($config['host']) . "</code><br>";
        echo "数据库: <code>" . htmlspecialchars($config['dbname']) . "</code><br>";
        echo "用户: <code>" . htmlspecialchars($config['user']) . "</code><br>";
        echo "</div>";
        
        // 先连接到MySQL服务器（不指定数据库）
        try {
            $dsn = "mysql:host={$config['host']};charset={$config['charset']}";
            $pdo = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            
            // 创建数据库（如果不存在）
            $dbname = $config['dbname'];
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            echo "<p><span class='ok'>✓</span> 数据库 <code>{$dbname}</code> 已准备就绪</p>";
            
            // 选择数据库
            $pdo->exec("USE `{$dbname}`");
            
        } catch (PDOException $e) {
            throw new Exception("数据库连接失败: " . $e->getMessage());
        }
        
        // 读取SQL文件
        $sqlFile = 'database/schema.sql';
        if (!file_exists($sqlFile)) {
            throw new Exception("SQL文件不存在: $sqlFile");
        }
        
        $sql = file_get_contents($sqlFile);
        
        // 移除CREATE DATABASE和USE语句（已经处理过了）
        $sql = preg_replace('/CREATE DATABASE[^;]+;/i', '', $sql);
        $sql = preg_replace('/USE[^;]+;/i', '', $sql);
        
        // 分割SQL语句
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            function($stmt) {
                $stmt = trim($stmt);
                // 过滤空语句和注释
                return !empty($stmt) && 
                       !preg_match('/^--/', $stmt) &&
                       !preg_match('/^\/\*/', $stmt) &&
                       strlen($stmt) > 10; // 至少10个字符
            }
        );
        
        $success = 0;
        $skipped = 0;
        $errors = [];
        $executedStatements = [];
        
        echo "<h3>执行SQL语句...</h3>";
        echo "<div style='max-height:300px;overflow-y:auto;background:#f9f9f9;padding:10px;border-radius:4px;'>";
        
        foreach ($statements as $index => $statement) {
            $statement = trim($statement);
            if (empty($statement)) continue;
            
            try {
                $pdo->exec($statement);
                $success++;
                $executedStatements[] = [
                    'type' => 'success',
                    'statement' => substr($statement, 0, 100) . '...'
                ];
            } catch (PDOException $e) {
                $errorMsg = $e->getMessage();
                // 忽略已存在的表/索引错误
                if (strpos($errorMsg, 'already exists') !== false || 
                    strpos($errorMsg, 'Duplicate key name') !== false ||
                    strpos($errorMsg, 'Duplicate entry') !== false) {
                    $skipped++;
                    $executedStatements[] = [
                        'type' => 'skipped',
                        'statement' => substr($statement, 0, 100) . '...',
                        'message' => '已存在，跳过'
                    ];
                } else {
                    $errors[] = [
                        'statement' => substr($statement, 0, 200),
                        'error' => $errorMsg
                    ];
                    $executedStatements[] = [
                        'type' => 'error',
                        'statement' => substr($statement, 0, 100) . '...',
                        'message' => $errorMsg
                    ];
                }
            }
        }
        
        echo "</div>";
        
        echo "<h3>执行结果</h3>";
        echo "<p><span class='ok'>✓</span> 成功执行: <strong>{$success}</strong> 条语句</p>";
        if ($skipped > 0) {
            echo "<p><span class='warning'>⚠</span> 跳过（已存在）: <strong>{$skipped}</strong> 条语句</p>";
        }
        if (count($errors) > 0) {
            echo "<p><span class='error'>✗</span> 错误: <strong>" . count($errors) . "</strong> 条语句</p>";
        }
        
        // 检查所有表
        echo "<h3>📊 表结构检查</h3>";
        $expectedTables = [
            'roles', 'users', 'categories', 'payment_methods', 'vendors',
            'transactions', 'attachments', 'void_requests',
            'cash_closings',
            'assets', 'asset_attachments',
            'materials', 'material_attachments',
            'drinks', 'drink_recipes', 'consumption_logs',
            'inspections', 'inspection_photos',
            'tasks', 'task_attachments',
            'employees', 'shifts'
        ];
        
        $existingTables = [];
        $missingTables = [];
        
        foreach ($expectedTables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '{$table}'");
            if ($stmt->rowCount() > 0) {
                $existingTables[] = $table;
                // 获取记录数
                try {
                    $countStmt = $pdo->query("SELECT COUNT(*) as cnt FROM `{$table}`");
                    $count = $countStmt->fetch()['cnt'];
                    echo "<p><span class='ok'>✓</span> <code>{$table}</code>: {$count} 条记录</p>";
                } catch (Exception $e) {
                    echo "<p><span class='ok'>✓</span> <code>{$table}</code>: 表存在</p>";
                }
            } else {
                $missingTables[] = $table;
                echo "<p><span class='error'>✗</span> <code>{$table}</code>: 表不存在</p>";
            }
        }
        
        echo "<h3>总结</h3>";
        echo "<p>已创建表: <strong>" . count($existingTables) . "</strong> / " . count($expectedTables) . "</p>";
        
        if (count($missingTables) > 0) {
            echo "<p class='error'>缺失的表: " . implode(', ', $missingTables) . "</p>";
        }
        
        if (count($errors) > 0) {
            echo "<h3>错误详情</h3>";
            echo "<div style='background:#fee;padding:15px;border-radius:4px;max-height:300px;overflow-y:auto;'>";
            foreach ($errors as $error) {
                echo "<p><strong>SQL:</strong> <code>" . htmlspecialchars($error['statement']) . "</code></p>";
                echo "<p><strong>错误:</strong> " . htmlspecialchars($error['error']) . "</p>";
                echo "<hr>";
            }
            echo "</div>";
        }
        
        if (count($existingTables) === count($expectedTables)) {
            echo "<div class='info' style='background:#d4edda;border-left-color:#28a745;margin-top:20px;'>";
            echo "<h3 style='color:#155724;margin-top:0;'>✅ 初始化成功！</h3>";
            echo "<p>所有表已创建完成。现在可以：</p>";
            echo "<ul>";
            echo "<li><a href='public/index.php?r=auth/login' style='color:#155724;font-weight:bold;'>前往登录页面</a></li>";
            echo "<li>默认用户名: <code>admin</code></li>";
            echo "<li>默认密码: <code>admin123</code></li>";
            echo "</ul>";
            echo "</div>";
        } else {
            echo "<div class='info' style='background:#fff3cd;border-left-color:#ffc107;margin-top:20px;'>";
            echo "<h3 style='color:#856404;margin-top:0;'>⚠️ 部分表创建失败</h3>";
            echo "<p>请检查错误信息并手动执行缺失表的创建语句。</p>";
            echo "</div>";
        }
        
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div class='section' style='border-color:red;background:#fee;'>";
        echo "<h2 style='color:red;'>❌ 初始化失败</h2>";
        echo "<p class='error'>" . nl2br(htmlspecialchars($e->getMessage())) . "</p>";
        echo "<h3>排查建议：</h3>";
        echo "<ul>";
        echo "<li>检查数据库配置文件 <code>app/config/db.php</code> 是否正确</li>";
        echo "<li>检查数据库用户是否有 CREATE TABLE 权限</li>";
        echo "<li>检查 MySQL 服务是否正常运行</li>";
        echo "<li>检查数据库名称是否正确</li>";
        echo "</ul>";
        echo "</div>";
    }
} else {
    // 显示配置检查
    echo "<div class='section'>";
    echo "<h2>📖 说明</h2>";
    echo "<p>此工具将执行 <code>database/schema.sql</code> 文件来初始化数据库结构。</p>";
    echo "<div class='info'>";
    echo "<strong>功能：</strong><br>";
    echo "• 自动创建数据库（如果不存在）<br>";
    echo "• 创建所有数据表（20个表）<br>";
    echo "• 插入初始数据（角色、分类、支付方式等）<br>";
    echo "• 检查表结构完整性<br>";
    echo "</div>";
    echo "<p><strong>注意:</strong> 如果表已存在，将跳过创建，不会影响现有数据。</p>";
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>🔍 数据库配置检查</h2>";
    if (file_exists('app/config/db.php')) {
        $config = require 'app/config/db.php';
        echo "<p>配置文件: <code>app/config/db.php</code> <span class='ok'>✓</span></p>";
        echo "<table>";
        echo "<tr><th>配置项</th><th>值</th></tr>";
        echo "<tr><td>主机</td><td><code>" . htmlspecialchars($config['host']) . "</code></td></tr>";
        echo "<tr><td>数据库</td><td><code>" . htmlspecialchars($config['dbname']) . "</code></td></tr>";
        echo "<tr><td>用户</td><td><code>" . htmlspecialchars($config['user']) . "</code></td></tr>";
        echo "<tr><td>字符集</td><td><code>" . htmlspecialchars($config['charset']) . "</code></td></tr>";
        echo "</table>";
        
        // 测试连接
        try {
            require_once 'app/core/DB.php';
            $conn = DB::conn();
            echo "<p style='margin-top:15px;'><span class='ok'>✓</span> 数据库连接成功</p>";
            
            // 检查数据库是否存在
            $dbname = $config['dbname'];
            $stmt = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$dbname}'");
            if ($stmt->rowCount() > 0) {
                echo "<p><span class='ok'>✓</span> 数据库 <code>{$dbname}</code> 已存在</p>";
                
                // 检查表
                $conn->exec("USE `{$dbname}`");
                $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                echo "<p>当前表数量: <strong>" . count($tables) . "</strong></p>";
                if (count($tables) > 0) {
                    echo "<p class='warning'>⚠️ 数据库已有表，执行初始化将跳过已存在的表</p>";
                }
            } else {
                echo "<p><span class='warning'>⚠️</span> 数据库 <code>{$dbname}</code> 不存在，将自动创建</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>✗</span> 数据库连接失败: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p>请检查：</p>";
            echo "<ul>";
            echo "<li>数据库服务是否运行</li>";
            echo "<li>数据库用户名和密码是否正确</li>";
            echo "<li>数据库用户是否有足够权限</li>";
            echo "</ul>";
        }
    } else {
        echo "<p class='error'>✗</span> 配置文件不存在: <code>app/config/db.php</code></p>";
        echo "<p>请执行以下步骤：</p>";
        echo "<ol>";
        echo "<li>复制 <code>app/config/db.php.example</code> 为 <code>app/config/db.php</code></li>";
        echo "<li>编辑 <code>app/config/db.php</code> 并配置数据库信息</li>";
        echo "<li>刷新此页面</li>";
        echo "</ol>";
    }
    echo "</div>";
    
    if (file_exists('database/schema.sql')) {
        $sqlSize = filesize('database/schema.sql');
        echo "<div class='section'>";
        echo "<h2>🚀 开始初始化</h2>";
        echo "<p>SQL文件: <code>database/schema.sql</code> <span class='ok'>✓</span> (" . number_format($sqlSize) . " 字节)</p>";
        echo "<p>点击下面的按钮开始初始化数据库：</p>";
        echo "<form method='post'>";
        echo "<button type='submit' name='init'>初始化数据库</button>";
        echo "</form>";
        echo "</div>";
    } else {
        echo "<div class='section'>";
        echo "<p class='error'>✗</span> SQL文件不存在: <code>database/schema.sql</code></p>";
        echo "</div>";
    }
}

echo "</body></html>";
