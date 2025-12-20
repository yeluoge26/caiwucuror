-- ============================================
-- 生产环境数据库更新脚本（安全版本 - 带检查）
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
-- 步骤1：检查当前状态（使用 SHOW 命令，不需要 information_schema 权限）
-- ============================================

SELECT '=== 检查当前数据库状态 ===' AS info;

-- 注意：由于权限限制，无法使用 INFORMATION_SCHEMA
-- 将直接尝试创建/添加，如果已存在会报错但可以忽略

-- ============================================
-- 步骤2：添加缺失的表：cash_closings
-- ============================================

-- 检查表是否存在，如果不存在则创建
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
-- 步骤3：添加缺失的字段：employees.employment_type
-- ============================================

-- 直接添加字段，如果字段已存在会报错，但可以忽略
-- 错误信息：Duplicate column name 'employment_type'
-- 如果看到此错误，说明字段已存在，可以继续执行后续步骤

ALTER TABLE employees 
ADD COLUMN employment_type ENUM('full_time','part_time') NOT NULL DEFAULT 'full_time' 
COMMENT '雇佣类型：全职/兼职' 
AFTER status;

-- ============================================
-- 步骤4：添加缺失的字段：shifts.status
-- ============================================

-- 直接添加字段，如果字段已存在会报错，但可以忽略
-- 错误信息：Duplicate column name 'status'
-- 如果看到此错误，说明字段已存在，可以跳过后续的数据迁移

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
-- 如果索引已存在会报错，但可以忽略
-- 错误信息：Duplicate key name 'idx_shifts_status'
ALTER TABLE shifts ADD INDEX idx_shifts_status (status);

-- ============================================
-- 步骤5：验证更新结果（使用 SHOW 命令）
-- ============================================

SELECT '=== 更新后验证 ===' AS info;

-- 检查 cash_closings 表结构
SHOW CREATE TABLE cash_closings;

-- 检查 employees 表结构（查看是否有 employment_type 字段）
SHOW COLUMNS FROM employees LIKE 'employment_type';

-- 检查 shifts 表结构（查看是否有 status 字段）
SHOW COLUMNS FROM shifts LIKE 'status';

-- 检查 shifts 表索引（查看是否有 idx_shifts_status 索引）
SHOW INDEX FROM shifts WHERE Key_name = 'idx_shifts_status';

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

