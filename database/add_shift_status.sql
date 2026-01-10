-- ============================================
-- 添加班次状态字段
-- ============================================
-- 此脚本为 shifts 表添加 status 字段，用于记录员工在岗状态
-- 状态包括：pending（未确认）、confirmed（已到岗）、late（迟到）、leave（请假）、off（调休）、abnormal（打卡异常）

-- 添加 status 字段
ALTER TABLE shifts 
ADD COLUMN status ENUM('pending', 'confirmed', 'late', 'leave', 'off', 'abnormal') 
NOT NULL DEFAULT 'pending' 
COMMENT '在岗状态：pending=未确认，confirmed=已到岗，late=迟到，leave=请假，off=调休，abnormal=打卡异常' 
AFTER is_confirmed;

-- 将现有的 is_confirmed 数据迁移到 status 字段
UPDATE shifts SET status = 'confirmed' WHERE is_confirmed = 1;
UPDATE shifts SET status = 'pending' WHERE is_confirmed = 0;

-- 添加索引以提高查询性能
ALTER TABLE shifts ADD INDEX idx_shifts_status (status);

-- ============================================
-- 执行完成！
-- ============================================
-- 验证命令（可选）：
-- SHOW COLUMNS FROM shifts LIKE 'status';
-- SELECT status, COUNT(*) FROM shifts GROUP BY status;






