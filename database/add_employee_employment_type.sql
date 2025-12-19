-- ============================================
-- 员工表 - 添加全职/兼职字段
-- 适用于已有数据的数据库
-- 
-- 重要提示：
-- 1. ⚠️ 执行前请先备份数据库！
-- 2. 此脚本使用 ALTER TABLE，会修改现有表结构
-- 3. 现有数据的 employment_type 将默认为 'full_time'
-- ============================================

-- 选择数据库（请根据实际情况修改数据库名）
USE coffee_finance;

-- ============================================
-- 添加 employment_type 字段到 employees 表
-- ============================================
ALTER TABLE employees 
ADD COLUMN employment_type ENUM('full_time','part_time') NOT NULL DEFAULT 'full_time' 
COMMENT '雇佣类型：全职/兼职' 
AFTER status;

-- ============================================
-- 执行完成！
-- 
-- 验证命令（可选，用于检查字段是否添加成功）：
-- DESC employees;
-- SHOW COLUMNS FROM employees LIKE 'employment_type';
-- ============================================

