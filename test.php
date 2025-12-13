<?php
/**
 * 系统功能测试脚本
 * 访问: http://localhost/test.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>财务系统功能测试</h1>";
echo "<style>body{font-family:monospace;padding:20px;} .ok{color:green;} .error{color:red;} .section{margin:20px 0;padding:10px;border:1px solid #ddd;}</style>";

// 测试1: PHP版本
echo "<div class='section'>";
echo "<h2>1. PHP版本检查</h2>";
$phpVersion = phpversion();
echo "PHP版本: <strong>$phpVersion</strong>";
if (version_compare($phpVersion, '7.4.0', '>=')) {
    echo " <span class='ok'>✓ 符合要求</span>";
} else {
    echo " <span class='error'>✗ 需要 PHP 7.4+</span>";
}
echo "</div>";

// 测试2: 必需扩展
echo "<div class='section'>";
echo "<h2>2. PHP扩展检查</h2>";
$required = ['pdo', 'pdo_mysql', 'mbstring', 'session'];
foreach ($required as $ext) {
    if (extension_loaded($ext)) {
        echo "<span class='ok'>✓</span> $ext<br>";
    } else {
        echo "<span class='error'>✗</span> $ext (缺失)<br>";
    }
}
echo "</div>";

// 测试3: 文件结构
echo "<div class='section'>";
echo "<h2>3. 文件结构检查</h2>";
$files = [
    'public/index.php',
    'app/config/app.php',
    'app/config/db.php',
    'app/core/DB.php',
    'app/core/Router.php',
    'app/core/Auth.php',
    'app/core/I18n.php',
    'app/core/Csrf.php',
    'lang/zh.php',
    'lang/vi.php',
    'database/schema.sql'
];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<span class='ok'>✓</span> $file<br>";
    } else {
        echo "<span class='error'>✗</span> $file (缺失)<br>";
    }
}
echo "</div>";

// 测试4: 数据库配置
echo "<div class='section'>";
echo "<h2>4. 数据库配置检查</h2>";
if (file_exists('app/config/db.php')) {
    $config = require 'app/config/db.php';
    echo "主机: " . $config['host'] . "<br>";
    echo "数据库: " . $config['dbname'] . "<br>";
    echo "用户: " . $config['user'] . "<br>";
    echo "字符集: " . $config['charset'] . "<br>";
} else {
    echo "<span class='error'>✗ 配置文件不存在</span>";
}
echo "</div>";

// 测试5: 数据库连接
echo "<div class='section'>";
echo "<h2>5. 数据库连接测试</h2>";
if (file_exists('app/core/DB.php')) {
    try {
        require_once 'app/core/DB.php';
        $conn = DB::conn();
        echo "<span class='ok'>✓</span> 数据库连接成功<br>";
        
        // 检查表是否存在
        $tables = ['roles', 'users', 'categories', 'payment_methods', 'vendors', 'transactions'];
        echo "<h3>数据表检查:</h3>";
        foreach ($tables as $table) {
            $stmt = $conn->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "<span class='ok'>✓</span> $table 表存在<br>";
            } else {
                echo "<span class='error'>✗</span> $table 表不存在 (需要导入 database/schema.sql)<br>";
            }
        }
        
        // 检查初始数据
        echo "<h3>初始数据检查:</h3>";
        $stmt = $conn->query("SELECT COUNT(*) as cnt FROM roles");
        $roles = $stmt->fetch()['cnt'];
        echo "角色数量: $roles " . ($roles >= 4 ? "<span class='ok'>✓</span>" : "<span class='error'>✗ 需要初始化数据</span>") . "<br>";
        
        $stmt = $conn->query("SELECT COUNT(*) as cnt FROM users");
        $users = $stmt->fetch()['cnt'];
        echo "用户数量: $users " . ($users >= 1 ? "<span class='ok'>✓</span>" : "<span class='error'>✗ 需要创建管理员用户</span>") . "<br>";
        
    } catch (Exception $e) {
        echo "<span class='error'>✗</span> 数据库连接失败: " . htmlspecialchars($e->getMessage()) . "<br>";
        echo "<p>请检查 app/config/db.php 中的数据库配置</p>";
    }
} else {
    echo "<span class='error'>✗</span> DB.php 文件不存在";
}
echo "</div>";

// 测试6: Session测试
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

// 测试7: 路由测试
echo "<div class='section'>";
echo "<h2>7. 路由测试</h2>";
$routes = [
    'auth/login' => 'AuthController::login',
    'transactions/list' => 'TransactionController::list',
    'transactions/create' => 'TransactionController::create',
    'reports/dashboard' => 'ReportController::dashboard',
];
foreach ($routes as $route => $controller) {
    $parts = explode('/', $route);
    $controllerFile = 'app/controllers/' . ucfirst($parts[0]) . 'Controller.php';
    if (file_exists($controllerFile)) {
        echo "<span class='ok'>✓</span> $route → $controller<br>";
    } else {
        echo "<span class='error'>✗</span> $route → 控制器文件不存在<br>";
    }
}
echo "</div>";

// 测试8: 语言包测试
echo "<div class='section'>";
echo "<h2>8. 语言包测试</h2>";
if (file_exists('app/core/I18n.php')) {
    require_once 'app/core/I18n.php';
    $_SESSION['lang'] = 'zh';
    I18n::init();
    $testKey = 'app.title';
    $result = __($testKey);
    if ($result !== $testKey) {
        echo "<span class='ok'>✓</span> 中文语言包: $result<br>";
    } else {
        echo "<span class='error'>✗</span> 中文语言包加载失败<br>";
    }
    
    $_SESSION['lang'] = 'vi';
    I18n::init();
    $result = __($testKey);
    if ($result !== $testKey) {
        echo "<span class='ok'>✓</span> 越南语语言包: $result<br>";
    } else {
        echo "<span class='error'>✗</span> 越南语语言包加载失败<br>";
    }
} else {
    echo "<span class='error'>✗</span> I18n.php 文件不存在<br>";
}
echo "</div>";

// 总结
echo "<div class='section' style='background:#f0f0f0;padding:20px;margin-top:30px;'>";
echo "<h2>测试总结</h2>";
echo "<p><strong>如果所有测试都通过，您可以：</strong></p>";
echo "<ol>";
echo "<li>访问登录页面: <a href='public/index.php?r=auth/login' target='_blank'>public/index.php?r=auth/login</a></li>";
echo "<li>默认账号: admin / admin123</li>";
echo "<li>如果数据库表不存在，请先导入: <code>mysql -u root -p coffee_finance < database/schema.sql</code></li>";
echo "</ol>";
echo "</div>";

