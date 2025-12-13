<?php
/**
 * 数据库初始化脚本
 * 使用方法: php init_db.php
 * 或在浏览器访问: http://localhost/init_db.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>数据库初始化</title>";
echo "<style>body{font-family:Arial,sans-serif;padding:20px;max-width:800px;margin:0 auto;}";
echo ".ok{color:green;} .error{color:red;} .section{margin:20px 0;padding:15px;border:1px solid #ddd;}";
echo "</style></head><body><h1>数据库初始化工具</h1>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['init'])) {
    try {
        require_once 'app/core/DB.php';
        $conn = DB::conn();
        
        echo "<div class='section'>";
        echo "<h2>开始初始化...</h2>";
        
        // 读取SQL文件
        $sqlFile = 'database/schema.sql';
        if (!file_exists($sqlFile)) {
            throw new Exception("SQL文件不存在: $sqlFile");
        }
        
        $sql = file_get_contents($sqlFile);
        
        // 分割SQL语句（简单处理）
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            function($stmt) {
                return !empty($stmt) && !preg_match('/^(--|CREATE DATABASE|USE )/i', $stmt);
            }
        );
        
        $success = 0;
        $errors = [];
        
        foreach ($statements as $statement) {
            if (empty(trim($statement))) continue;
            
            try {
                $conn->exec($statement);
                $success++;
            } catch (PDOException $e) {
                // 忽略已存在的表错误
                if (strpos($e->getMessage(), 'already exists') === false && 
                    strpos($e->getMessage(), 'Duplicate entry') === false) {
                    $errors[] = $e->getMessage();
                }
            }
        }
        
        echo "<p><span class='ok'>✓</span> 执行了 $success 条SQL语句</p>";
        
        if (!empty($errors)) {
            echo "<p><span class='error'>✗</span> 错误:</p><ul>";
            foreach ($errors as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>";
            }
            echo "</ul>";
        }
        
        // 检查结果
        echo "<h3>检查结果:</h3>";
        $tables = ['roles', 'users', 'categories', 'payment_methods', 'vendors', 'transactions'];
        foreach ($tables as $table) {
            $stmt = $conn->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                $countStmt = $conn->query("SELECT COUNT(*) as cnt FROM $table");
                $count = $countStmt->fetch()['cnt'];
                echo "<p><span class='ok'>✓</span> $table: $count 条记录</p>";
            } else {
                echo "<p><span class='error'>✗</span> $table 表不存在</p>";
            }
        }
        
        echo "<p><strong>初始化完成！</strong> <a href='public/index.php?r=auth/login'>前往登录页面</a></p>";
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div class='section' style='border-color:red;'>";
        echo "<h2>初始化失败</h2>";
        echo "<p class='error'>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo "</div>";
    }
} else {
    echo "<div class='section'>";
    echo "<h2>说明</h2>";
    echo "<p>此工具将执行 database/schema.sql 文件来初始化数据库结构。</p>";
    echo "<p><strong>注意:</strong> 如果表已存在，将跳过创建，但会插入初始数据。</p>";
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>数据库配置检查</h2>";
    if (file_exists('app/config/db.php')) {
        $config = require 'app/config/db.php';
        echo "<p>主机: <code>" . htmlspecialchars($config['host']) . "</code></p>";
        echo "<p>数据库: <code>" . htmlspecialchars($config['dbname']) . "</code></p>";
        echo "<p>用户: <code>" . htmlspecialchars($config['user']) . "</code></p>";
        
        // 测试连接
        try {
            require_once 'app/core/DB.php';
            $conn = DB::conn();
            echo "<p><span class='ok'>✓</span> 数据库连接成功</p>";
        } catch (Exception $e) {
            echo "<p><span class='error'>✗</span> 数据库连接失败: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p class='error'>配置文件不存在</p>";
    }
    echo "</div>";
    
    if (file_exists('database/schema.sql')) {
        echo "<form method='post' class='section'>";
        echo "<h2>开始初始化</h2>";
        echo "<p>点击下面的按钮开始初始化数据库:</p>";
        echo "<button type='submit' name='init' style='padding:10px 20px;background:#4caf50;color:white;border:none;border-radius:4px;cursor:pointer;font-size:16px;'>初始化数据库</button>";
        echo "</form>";
    } else {
        echo "<div class='section'>";
        echo "<p class='error'>SQL文件不存在: database/schema.sql</p>";
        echo "</div>";
    }
}

echo "</body></html>";

