<?php
/**
 * 测试店长管理功能
 * 访问：http://localhost/test_manager_features.php
 */

require_once __DIR__ . '/app/config/app.php';
require_once __DIR__ . '/app/config/db.php';
require_once __DIR__ . '/app/core/DB.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>功能测试 - 店长管理系统</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 20px auto; padding: 20px; }
        .test-section { background: #f5f5f5; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .success { color: #27ae60; }
        .error { color: #e74c3c; }
        .info { color: #3498db; }
        pre { background: white; padding: 10px; border-radius: 4px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        th { background: #3498db; color: white; }
    </style>
</head>
<body>
    <h1>店长管理系统功能测试</h1>
    
    <?php
    try {
        $db = DB::conn();
        echo '<div class="test-section">';
        echo '<h2>✅ 数据库连接成功</h2>';
        echo '</div>';
        
        // 测试1: 检查cash_closings表是否存在
        echo '<div class="test-section">';
        echo '<h2>测试1: 检查 cash_closings 表</h2>';
        try {
            $tableExists = $db->query("SHOW TABLES LIKE 'cash_closings'")->fetch();
            if ($tableExists) {
                echo '<p class="success">✅ cash_closings 表已存在</p>';
                
                // 显示表结构
                $columns = $db->query("SHOW COLUMNS FROM cash_closings")->fetchAll();
                echo '<h3>表结构：</h3>';
                echo '<table>';
                echo '<tr><th>字段名</th><th>类型</th><th>允许NULL</th><th>默认值</th></tr>';
                foreach ($columns as $col) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($col['Field']) . '</td>';
                    echo '<td>' . htmlspecialchars($col['Type']) . '</td>';
                    echo '<td>' . htmlspecialchars($col['Null']) . '</td>';
                    echo '<td>' . htmlspecialchars($col['Default'] ?? 'NULL') . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p class="error">❌ cash_closings 表不存在</p>';
                echo '<p class="info">请先执行迁移脚本：<a href="/migrate_cash_closings.php">执行迁移</a></p>';
            }
        } catch (Exception $e) {
            echo '<p class="error">❌ 检查失败：' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        echo '</div>';
        
        // 测试2: 检查模型文件
        echo '<div class="test-section">';
        echo '<h2>测试2: 检查模型文件</h2>';
        $modelFiles = [
            'app/models/CashClosing.php' => 'CashClosing模型',
            'app/models/PaymentMethod.php' => 'PaymentMethod模型',
            'app/models/Transaction.php' => 'Transaction模型',
        ];
        
        foreach ($modelFiles as $file => $name) {
            if (file_exists($file)) {
                echo '<p class="success">✅ ' . $name . ' 存在</p>';
            } else {
                echo '<p class="error">❌ ' . $name . ' 不存在</p>';
            }
        }
        echo '</div>';
        
        // 测试3: 检查控制器文件
        echo '<div class="test-section">';
        echo '<h2>测试3: 检查控制器文件</h2>';
        $controllerFiles = [
            'app/controllers/CashClosingController.php' => 'CashClosingController',
            'app/controllers/ManagerController.php' => 'ManagerController',
        ];
        
        foreach ($controllerFiles as $file => $name) {
            if (file_exists($file)) {
                echo '<p class="success">✅ ' . $name . ' 存在</p>';
            } else {
                echo '<p class="error">❌ ' . $name . ' 不存在</p>';
            }
        }
        echo '</div>';
        
        // 测试4: 检查视图文件
        echo '<div class="test-section">';
        echo '<h2>测试4: 检查视图文件</h2>';
        $viewFiles = [
            'app/views/cash_closings/create.php' => '现金日结创建视图',
            'app/views/cash_closings/list.php' => '现金日结列表视图',
            'app/views/cash_closings/view.php' => '现金日结详情视图',
            'app/views/manager/dashboard.php' => '店长首页视图',
        ];
        
        foreach ($viewFiles as $file => $name) {
            if (file_exists($file)) {
                echo '<p class="success">✅ ' . $name . ' 存在</p>';
            } else {
                echo '<p class="error">❌ ' . $name . ' 不存在</p>';
            }
        }
        echo '</div>';
        
        // 测试5: 检查路由配置
        echo '<div class="test-section">';
        echo '<h2>测试5: 检查路由配置</h2>';
        $routerFile = 'app/core/Router.php';
        if (file_exists($routerFile)) {
            $routerContent = file_get_contents($routerFile);
            if (strpos($routerContent, 'cash_closings') !== false && strpos($routerContent, 'manager') !== false) {
                echo '<p class="success">✅ 路由配置已更新（包含 cash_closings 和 manager）</p>';
            } else {
                echo '<p class="error">❌ 路由配置未更新</p>';
            }
        } else {
            echo '<p class="error">❌ Router.php 不存在</p>';
        }
        echo '</div>';
        
        // 测试6: 检查国际化文件
        echo '<div class="test-section">';
        echo '<h2>测试6: 检查国际化翻译</h2>';
        $langFiles = ['lang/zh.php', 'lang/vi.php'];
        foreach ($langFiles as $file) {
            if (file_exists($file)) {
                $content = file_get_contents($file);
                if (strpos($content, 'cash_closing') !== false && strpos($content, 'manager.dashboard') !== false) {
                    echo '<p class="success">✅ ' . basename($file) . ' 已更新</p>';
                } else {
                    echo '<p class="error">❌ ' . basename($file) . ' 未更新</p>';
                }
            } else {
                echo '<p class="error">❌ ' . basename($file) . ' 不存在</p>';
            }
        }
        echo '</div>';
        
        // 测试7: 检查PaymentMethod是否有findByName方法
        echo '<div class="test-section">';
        echo '<h2>测试7: 检查PaymentMethod模型方法</h2>';
        $pmFile = 'app/models/PaymentMethod.php';
        if (file_exists($pmFile)) {
            $content = file_get_contents($pmFile);
            if (strpos($content, 'findByName') !== false) {
                echo '<p class="success">✅ PaymentMethod::findByName() 方法存在</p>';
            } else {
                echo '<p class="error">❌ PaymentMethod::findByName() 方法不存在</p>';
            }
        }
        echo '</div>';
        
        // 测试8: 检查Transaction模型的getSummary方法
        echo '<div class="test-section">';
        echo '<h2>测试8: 检查Transaction模型方法</h2>';
        $txFile = 'app/models/Transaction.php';
        if (file_exists($txFile)) {
            $content = file_get_contents($txFile);
            if (strpos($content, 'payment_method_id') !== false && strpos($content, 'getSummary') !== false) {
                echo '<p class="success">✅ Transaction::getSummary() 支持 payment_method_id 过滤器</p>';
            } else {
                echo '<p class="error">❌ Transaction::getSummary() 未更新</p>';
            }
        }
        echo '</div>';
        
        echo '<div class="test-section">';
        echo '<h2>📋 下一步操作</h2>';
        echo '<ol>';
        echo '<li>如果 cash_closings 表不存在，请访问 <a href="/migrate_cash_closings.php">迁移脚本</a> 执行数据库迁移</li>';
        echo '<li>以店长角色登录系统，访问 <a href="/index.php?r=manager/dashboard">店长首页</a></li>';
        echo '<li>测试现金日结功能：<a href="/index.php?r=cash_closings/create">创建现金日结</a></li>';
        echo '<li>测试巡店功能增强：<a href="/index.php?r=inspections/create">创建巡店</a></li>';
        echo '</ol>';
        echo '</div>';
        
    } catch (Exception $e) {
        echo '<div class="test-section">';
        echo '<h2 class="error">❌ 数据库连接失败</h2>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        echo '</div>';
    }
    ?>
    
    <p style="margin-top: 30px;">
        <a href="/index.php">返回首页</a> | 
        <a href="/migrate_cash_closings.php">执行数据库迁移</a>
    </p>
</body>
</html>

