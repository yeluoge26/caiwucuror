<?php
/**
 * 现金日结表迁移脚本
 * 执行方式：php database/migrate_cash_closings.php
 */

require_once __DIR__ . '/../app/config/app.php';
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/core/DB.php';

echo "开始执行现金日结表迁移...\n\n";

try {
    $db = DB::conn();
    
    // 检查表是否已存在
    $checkTable = $db->query("SHOW TABLES LIKE 'cash_closings'")->fetch();
    if ($checkTable) {
        echo "⚠️  警告：cash_closings 表已存在！\n";
        echo "是否继续？这将不会删除现有数据，只会创建不存在的表。\n";
        echo "按 Enter 继续，或 Ctrl+C 取消...\n";
        // 在命令行中等待用户输入
        // 这里直接继续执行
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
    
    echo "✅ 现金日结表创建成功！\n\n";
    
    // 验证表结构
    $columns = $db->query("SHOW COLUMNS FROM cash_closings")->fetchAll();
    echo "表结构验证：\n";
    echo "字段数量：" . count($columns) . "\n";
    foreach ($columns as $col) {
        echo "  - {$col['Field']} ({$col['Type']})\n";
    }
    
    echo "\n✅ 迁移完成！\n";
    
} catch (Exception $e) {
    echo "❌ 迁移失败：\n";
    echo $e->getMessage() . "\n";
    exit(1);
}



