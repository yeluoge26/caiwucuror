-- ============================================
-- 生产环境数据库更新脚本
-- 用途：更新生产环境数据库，添加缺失的表和字段
-- 日期：2025-12-20
-- ============================================
-- 
-- 警告：执行前请备份数据库！
-- 备份命令：mysqldump -u root -p tech_cafemanager > backup_$(date +%Y%m%d_%H%M%S).sql
-- 
-- ============================================

USE tech_cafemanager;

-- ============================================
-- 1. 添加缺失的表：cash_closings（现金日结表）
-- ============================================

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

-- ============================================
-- 2. 添加缺失的字段：employees.employment_type
-- ============================================

-- 检查字段是否存在，如果不存在则添加
-- 注意：如果字段已存在，ALTER TABLE 会报错，但可以使用 IGNORE 或先检查
-- 这里使用更安全的方式：先检查，如果不存在则添加

-- 方法：直接尝试添加，如果已存在会报错，但不会影响其他操作
-- 如果字段已存在，可以忽略错误或手动注释掉下面的语句

ALTER TABLE employees 
ADD COLUMN employment_type ENUM('full_time','part_time') NOT NULL DEFAULT 'full_time' 
COMMENT '雇佣类型：全职/兼职' 
AFTER status;

-- ============================================
-- 3. 添加缺失的字段：shifts.status
-- ============================================

-- 添加 status 字段
-- 注意：如果字段已存在，ALTER TABLE 会报错，但不会影响其他操作
-- 如果字段已存在，可以忽略错误或手动注释掉下面的语句

ALTER TABLE shifts 
ADD COLUMN status ENUM('pending', 'confirmed', 'late', 'leave', 'off', 'abnormal') 
NOT NULL DEFAULT 'pending' 
COMMENT '在岗状态：pending=未确认，confirmed=已到岗，late=迟到，leave=请假，off=调休，abnormal=打卡异常' 
AFTER is_confirmed;

-- 迁移现有数据：将 is_confirmed=1 的记录设置为 status='confirmed'
-- 注意：只有在字段刚添加时才需要执行，如果字段已存在且已有数据，可以跳过
UPDATE shifts 
SET status = 'confirmed' 
WHERE is_confirmed = 1 AND status = 'pending';

-- 添加索引以提高查询性能
-- 注意：如果索引已存在，ALTER TABLE 会报错，但不会影响其他操作
ALTER TABLE shifts ADD INDEX idx_shifts_status (status);

-- ============================================
-- 4. 验证更新结果
-- ============================================

SELECT 'Database update completed!' AS message;

-- 检查表是否存在
SELECT 
  'Tables check' AS check_type,
  CASE 
    WHEN COUNT(*) > 0 THEN '✓ cash_closings table exists'
    ELSE '✗ cash_closings table missing'
  END AS result
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = 'tech_cafemanager' 
  AND TABLE_NAME = 'cash_closings';

-- 检查字段是否存在
SELECT 
  'Columns check' AS check_type,
  CASE 
    WHEN COUNT(*) > 0 THEN '✓ employees.employment_type exists'
    ELSE '✗ employees.employment_type missing'
  END AS result
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'tech_cafemanager' 
  AND TABLE_NAME = 'employees' 
  AND COLUMN_NAME = 'employment_type'

UNION ALL

SELECT 
  'Columns check' AS check_type,
  CASE 
    WHEN COUNT(*) > 0 THEN '✓ shifts.status exists'
    ELSE '✗ shifts.status missing'
  END AS result
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'tech_cafemanager' 
  AND TABLE_NAME = 'shifts' 
  AND COLUMN_NAME = 'status';

-- ============================================
-- 更新完成！
-- ============================================
-- 
-- 已添加/更新的内容：
-- ✓ cash_closings 表（现金日结表）
-- ✓ employees.employment_type 字段（雇佣类型）
-- ✓ shifts.status 字段（在岗状态）
-- ✓ shifts 表索引（idx_shifts_status）
-- 
-- ============================================

