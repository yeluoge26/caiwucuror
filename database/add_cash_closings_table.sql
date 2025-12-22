-- 现金日结表迁移脚本
-- 执行前请确保已创建数据库并选择正确的数据库

USE coffee_finance;

-- 现金日结表
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='现金日结表';



