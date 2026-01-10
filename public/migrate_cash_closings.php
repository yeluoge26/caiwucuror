<?php
/**
 * 现金日结表迁移脚本（Web版本）
 * 访问：http://localhost/migrate_cash_closings.php
 * 注意：执行后请删除此文件以确保安全
 */

session_start();

require_once __DIR__ . '/../app/config/app.php';
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/core/DB.php';
require_once __DIR__ . '/../app/core/Auth.php';

// 安全检查：需要管理员权限
if (!Auth::check() || !in_array(Auth::user()['role_key'], ['owner'])) {
    die('权限不足！只有老板角色可以执行迁移。');
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>数据库迁移 - 现金日结表</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: #27ae60; background: #d4edda; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .error { color: #e74c3c; background: #f8d7da; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .info { color: #0c5460; background: #d1ecf1; padding: 15px; border-radius: 4px; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 4px; overflow-x: auto; }
        button { background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
        button:hover { background: #2980b9; }
    </style>
</head>
<body>
    <h1>现金日结表迁移</h1>
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['execute'])) {
        try {
            $db = DB::conn();
            
            // 检查表是否已存在
            $checkTable = $db->query("SHOW TABLES LIKE 'cash_closings'")->fetch();
            if ($checkTable) {
                echo '<div class="info">⚠️ 警告：cash_closings 表已存在！将跳过创建。</div>';
            }
            
            // 创建现金日结表
            $sql = "
            CREATE TABLE IF NOT EXISTS cash_closings (
              id INT AUTO_INCREMENT PRIMARY KEY,
              date DATE NOT NULL COMMENT '交账日期',
              created_by INT NOT NULL COMMENT '店长ID',
              cash_counted DECIMAL(12,2) NOT NULL COMMENT '实盘现金',
              cash_note TEXT NULL COMMENT '差额说明',
              status ENUM('draft','submitted','approved','rejected') NOT NULL DEFAULT 'draft' COMMENT '状态',
              submitted_at DATETIME NULL COMMENT '提交时间',
              reviewed_by INT NULL COMMENT '审核人ID',
              reviewed_at DATETIME NULL COMMENT '审核时间',
              review_note TEXT NULL COMMENT '审核备注',
              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
              updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
              CONSTRAINT fk_cash_closing_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
              CONSTRAINT fk_cash_closing_reviewer FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
              UNIQUE KEY uk_cash_closing_date (date),
              INDEX idx_cash_closing_status (status),
              INDEX idx_cash_closing_date (date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='现金日结表'
            ";
            
            $db->exec($sql);
            
            echo '<div class="success">✅ 现金日结表创建成功！</div>';
            
            // 验证表结构
            $columns = $db->query("SHOW COLUMNS FROM cash_closings")->fetchAll();
            echo '<div class="info">';
            echo '<h3>表结构验证：</h3>';
            echo '<p>字段数量：' . count($columns) . '</p>';
            echo '<pre>';
            foreach ($columns as $col) {
                echo "  - {$col['Field']} ({$col['Type']})\n";
            }
            echo '</pre>';
            echo '</div>';
            
            echo '<div class="success">✅ 迁移完成！</div>';
            echo '<p><strong>安全提示：</strong>迁移完成后，请删除此文件（public/migrate_cash_closings.php）以确保安全。</p>';
            
        } catch (Exception $e) {
            echo '<div class="error">❌ 迁移失败：</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
    } else {
        // 显示当前状态
        try {
            $db = DB::conn();
            $checkTable = $db->query("SHOW TABLES LIKE 'cash_closings'")->fetch();
            
            if ($checkTable) {
                echo '<div class="info">ℹ️ cash_closings 表已存在</div>';
                
                // 显示表结构
                $columns = $db->query("SHOW COLUMNS FROM cash_closings")->fetchAll();
                echo '<h3>当前表结构：</h3>';
                echo '<pre>';
                foreach ($columns as $col) {
                    echo "  - {$col['Field']} ({$col['Type']})\n";
                }
                echo '</pre>';
            } else {
                echo '<div class="info">ℹ️ cash_closings 表不存在，可以执行迁移</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">❌ 检查失败：' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
        
        <form method="post" style="margin-top: 20px;">
            <button type="submit" name="execute" onclick="return confirm('确定要执行迁移吗？')">
                执行迁移
            </button>
        </form>
        <?php
    }
    ?>
    
    <p style="margin-top: 30px;">
        <a href="/index.php?r=reports/dashboard">返回首页</a>
    </p>
</body>
</html>






