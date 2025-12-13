<?php
/**
 * 系统功能测试页面
 * 访问: http://localhost/public/test.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>系统测试</title>";
echo "<style>body{font-family:Arial,sans-serif;padding:20px;max-width:1000px;margin:0 auto;}";
echo ".ok{color:green;font-weight:bold;} .error{color:red;font-weight:bold;}";
echo ".section{margin:20px 0;padding:15px;border:1px solid #ddd;border-radius:5px;background:#f9f9f9;}";
echo "h1{color:#2c3e50;} h2{color:#34495e;border-bottom:2px solid #3498db;padding-bottom:5px;}";
echo "code{background:#f4f4f4;padding:2px 6px;border-radius:3px;}</style></head><body>";

echo "<h1>🧪 财务系统功能测试</h1>";

// 测试1: PHP版本
echo "<div class='section'>";
echo "<h2>1. PHP版本检查</h2>";
$phpVersion = phpversion();
echo "PHP版本: <strong>$phpVersion</strong> ";
if (version_compare($phpVersion, '7.4.0', '>=')) {
    echo "<span class='ok'>✓ 符合要求</span>";
} else {
    echo "<span class='error'>✗ 需要 PHP 7.4+</span>";
}
echo "</div>";

// 测试2: 必需扩展
echo "<div class='section'>";
echo "<h2>2. PHP扩展检查</h2>";
$required = ['pdo' => 'PDO', 'pdo_mysql' => 'PDO MySQL', 'mbstring' => 'mbstring', 'session' => 'Session'];
foreach ($required as $ext => $name) {
    if (extension_loaded($ext)) {
        echo "<span class='ok'>✓</span> $name<br>";
    } else {
        echo "<span class='error'>✗</span> $name (缺失)<br>";
    }
}
echo "</div>";

// 测试3: 文件结构
echo "<div class='section'>";
echo "<h2>3. 核心文件检查</h2>";
$files = [
    '../app/config/app.php' => '应用配置',
    '../app/config/db.php' => '数据库配置',
    '../app/core/DB.php' => '数据库类',
    '../app/core/Router.php' => '路由类',
    '../app/core/Auth.php' => '认证类',
    '../app/core/I18n.php' => '国际化类',
    '../lang/zh.php' => '中文语言包',
    '../lang/vi.php' => '越南语语言包',
];
$fileOk = 0;
foreach ($files as $file => $desc) {
    if (file_exists($file)) {
        echo "<span class='ok'>✓</span> $desc<br>";
        $fileOk++;
    } else {
        echo "<span class='error'>✗</span> $desc ($file 不存在)<br>";
    }
}
echo "<br>文件完整性: $fileOk/" . count($files);
echo "</div>";

// 测试4: 数据库配置
echo "<div class='section'>";
echo "<h2>4. 数据库配置</h2>";
if (file_exists('../app/config/db.php')) {
    $config = require '../app/config/db.php';
    echo "主机: <code>" . htmlspecialchars($config['host']) . "</code><br>";
    echo "数据库: <code>" . htmlspecialchars($config['dbname']) . "</code><br>";
    echo "用户: <code>" . htmlspecialchars($config['user']) . "</code><br>";
    echo "字符集: <code>" . htmlspecialchars($config['charset']) . "</code><br>";
} else {
    echo "<span class='error'>✗</span> 配置文件不存在";
}
echo "</div>";

// 测试5: 数据库连接
echo "<div class='section'>";
echo "<h2>5. 数据库连接测试</h2>";
if (file_exists('../app/core/DB.php')) {
    try {
        require_once '../app/core/DB.php';
        $conn = DB::conn();
        echo "<span class='ok'>✓</span> 数据库连接成功！<br><br>";
        
        // 检查表
        $tables = [
            'roles' => '角色表',
            'users' => '用户表',
            'categories' => '分类表',
            'payment_methods' => '支付方式表',
            'vendors' => '供应商表',
            'transactions' => '交易表'
        ];
        
        echo "<h3>数据表检查:</h3>";
        $tableOk = 0;
        foreach ($tables as $table => $desc) {
            $stmt = $conn->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "<span class='ok'>✓</span> $desc ($table)<br>";
                $tableOk++;
            } else {
                echo "<span class='error'>✗</span> $desc ($table) 不存在<br>";
            }
        }
        
        if ($tableOk < count($tables)) {
            echo "<br><strong>提示:</strong> 请执行以下命令导入数据库结构：<br>";
            echo "<code>mysql -u root -p coffee_finance < database/schema.sql</code>";
        }
        
        // 检查初始数据
        if ($tableOk > 0) {
            echo "<h3>初始数据检查:</h3>";
            try {
                $stmt = $conn->query("SELECT COUNT(*) as cnt FROM roles");
                $roles = $stmt->fetch()['cnt'];
                echo "角色数量: <strong>$roles</strong> ";
                echo ($roles >= 4 ? "<span class='ok'>✓</span>" : "<span class='error'>✗ 需要初始化数据</span>") . "<br>";
                
                $stmt = $conn->query("SELECT COUNT(*) as cnt FROM users");
                $users = $stmt->fetch()['cnt'];
                echo "用户数量: <strong>$users</strong> ";
                echo ($users >= 1 ? "<span class='ok'>✓</span>" : "<span class='error'>✗ 需要创建管理员用户</span>") . "<br>";
                
                if ($users > 0) {
                    $stmt = $conn->query("SELECT username, display_name FROM users LIMIT 5");
                    echo "<br>现有用户:<br>";
                    while ($row = $stmt->fetch()) {
                        echo "- " . htmlspecialchars($row['display_name']) . " (" . htmlspecialchars($row['username']) . ")<br>";
                    }
                }
            } catch (Exception $e) {
                echo "<span class='error'>✗</span> 查询数据失败: " . htmlspecialchars($e->getMessage()) . "<br>";
            }
        }
        
    } catch (Exception $e) {
        echo "<span class='error'>✗</span> 数据库连接失败: " . htmlspecialchars($e->getMessage()) . "<br>";
        echo "<p><strong>解决方案:</strong></p>";
        echo "<ol>";
        echo "<li>检查 MySQL 服务是否运行</li>";
        echo "<li>检查 <code>app/config/db.php</code> 中的数据库配置</li>";
        echo "<li>确认数据库 <code>coffee_finance</code> 已创建</li>";
        echo "</ol>";
    }
} else {
    echo "<span class='error'>✗</span> DB.php 文件不存在";
}
echo "</div>";

// 测试6: Session
echo "<div class='section'>";
echo "<h2>6. Session测试</h2>";
session_start();
$_SESSION['test'] = 'ok';
if (isset($_SESSION['test']) && $_SESSION['test'] === 'ok') {
    echo "<span class='ok'>✓</span> Session 工作正常<br>";
    unset($_SESSION['test']);
} else {
    echo "<span class='error'>✗</span> Session 无法工作<br>";
}
echo "</div>";

// 测试7: 路由
echo "<div class='section'>";
echo "<h2>7. 路由和控制器检查</h2>";
$routes = [
    'auth/login' => 'AuthController',
    'transactions/list' => 'TransactionController',
    'transactions/create' => 'TransactionController',
    'reports/dashboard' => 'ReportController',
    'settings/categories' => 'SettingController',
];
$routeOk = 0;
foreach ($routes as $route => $controller) {
    $parts = explode('/', $route);
    $controllerFile = '../app/controllers/' . ucfirst($parts[0]) . 'Controller.php';
    if (file_exists($controllerFile)) {
        echo "<span class='ok'>✓</span> $route → $controller<br>";
        $routeOk++;
    } else {
        echo "<span class='error'>✗</span> $route → 控制器文件不存在<br>";
    }
}
echo "</div>";

// 测试8: 语言包
echo "<div class='section'>";
echo "<h2>8. 语言包测试</h2>";
if (file_exists('../app/core/I18n.php')) {
    require_once '../app/core/I18n.php';
    $_SESSION['lang'] = 'zh';
    I18n::init();
    $testKey = 'app.title';
    $result = __($testKey);
    if ($result !== $testKey && !empty($result)) {
        echo "<span class='ok'>✓</span> 中文: " . htmlspecialchars($result) . "<br>";
    } else {
        echo "<span class='error'>✗</span> 中文语言包加载失败<br>";
    }
    
    $_SESSION['lang'] = 'vi';
    I18n::init();
    $result = __($testKey);
    if ($result !== $testKey && !empty($result)) {
        echo "<span class='ok'>✓</span> 越南语: " . htmlspecialchars($result) . "<br>";
    } else {
        echo "<span class='error'>✗</span> 越南语语言包加载失败<br>";
    }
} else {
    echo "<span class='error'>✗</span> I18n.php 文件不存在<br>";
}
echo "</div>";

// 总结和下一步
echo "<div class='section' style='background:#e8f5e9;border-color:#4caf50;'>";
echo "<h2>✅ 测试完成</h2>";

if ($fileOk === count($files) && isset($conn)) {
    echo "<p><strong>系统基本配置正常！</strong></p>";
    echo "<h3>下一步操作:</h3>";
    echo "<ol>";
    echo "<li><strong>访问登录页面:</strong> <a href='index.php?r=auth/login' target='_blank' style='color:#2196f3;'>点击这里登录</a></li>";
    echo "<li><strong>默认账号:</strong> <code>admin</code> / <code>admin123</code></li>";
    if (isset($tableOk) && $tableOk < count($tables)) {
        echo "<li><strong>导入数据库:</strong> 如果表不存在，请执行 <code>mysql -u root -p coffee_finance < ../database/schema.sql</code></li>";
    }
    echo "</ol>";
} else {
    echo "<p><strong>请先解决上述问题后再使用系统。</strong></p>";
}
echo "</div>";

echo "</body></html>";

