-- ============================================
-- 清理测试数据脚本
-- 用途：删除所有测试数据，准备部署到生产环境
-- 警告：此脚本会删除所有业务数据，请谨慎使用！
-- 执行前请务必备份数据库！
-- ============================================

USE coffee_finance;

-- ============================================
-- 1. 删除业务数据（按依赖关系顺序）
-- ============================================

-- 1.1 删除附件数据（先删除附件，因为外键约束）
DELETE FROM attachments;
DELETE FROM inspection_photos;
DELETE FROM task_attachments;
DELETE FROM asset_attachments;
DELETE FROM material_attachments;

-- 1.2 删除业务记录
DELETE FROM transactions;
DELETE FROM void_requests;
DELETE FROM inspections;
DELETE FROM tasks;
DELETE FROM cash_closings;
DELETE FROM consumption_logs;

-- 1.3 删除资产和材料记录（如果这些是测试数据）
DELETE FROM assets;
DELETE FROM materials;

-- 1.4 删除班次记录（保留员工表，只删除班次）
DELETE FROM shifts;

-- ============================================
-- 2. 清理测试员工数据（可选）
-- 注意：如果 insert_sample_employees.sql 创建的测试员工需要删除
-- 请根据实际情况修改下面的条件
-- ============================================

-- 删除测试员工（示例：删除display_name包含"测试"或"Test"的员工）
-- DELETE FROM employees WHERE display_name LIKE '%测试%' OR display_name LIKE '%Test%' OR display_name LIKE '%test%';

-- 或者删除所有员工（如果都是测试数据）
-- DELETE FROM employees;

-- ============================================
-- 3. 重置自增ID（可选，让新数据从1开始）
-- ============================================

-- 重置交易表自增ID
ALTER TABLE transactions AUTO_INCREMENT = 1;

-- 重置巡店表自增ID
ALTER TABLE inspections AUTO_INCREMENT = 1;

-- 重置任务表自增ID
ALTER TABLE tasks AUTO_INCREMENT = 1;

-- 重置现金日结表自增ID
ALTER TABLE cash_closings AUTO_INCREMENT = 1;

-- 重置附件表自增ID
ALTER TABLE attachments AUTO_INCREMENT = 1;
ALTER TABLE inspection_photos AUTO_INCREMENT = 1;
ALTER TABLE task_attachments AUTO_INCREMENT = 1;
ALTER TABLE asset_attachments AUTO_INCREMENT = 1;
ALTER TABLE material_attachments AUTO_INCREMENT = 1;

-- 重置消耗记录表自增ID
ALTER TABLE consumption_logs AUTO_INCREMENT = 1;

-- 重置班次表自增ID
ALTER TABLE shifts AUTO_INCREMENT = 1;

-- ============================================
-- 4. 清理上传的文件（需要手动执行）
-- ============================================
-- 注意：以下目录中的文件需要手动删除：
-- - public/uploads/ (所有上传的文件)
-- - public/uploads/inspections/ (巡店照片)
-- 
-- 或者使用以下命令（在服务器上执行）：
-- rm -rf public/uploads/*
-- 但保留目录结构：
-- mkdir -p public/uploads/inspections
-- chmod 755 public/uploads public/uploads/inspections

-- ============================================
-- 清理完成
-- ============================================
-- 
-- 保留的数据：
-- ✓ roles - 角色表（系统必需）
-- ✓ users - 用户表（包括管理员账户）
-- ✓ categories - 分类表（基础配置）
-- ✓ payment_methods - 支付方式表（基础配置）
-- ✓ vendors - 供应商表（基础配置）
-- ✓ drinks - 饮品表（基础配置）
-- ✓ drink_recipes - 配方表（基础配置）
-- ✓ employees - 员工表（如果未删除）
-- 
-- 已删除的数据：
-- ✗ transactions - 所有交易记录
-- ✗ inspections - 所有巡店记录
-- ✗ inspection_photos - 所有巡店照片
-- ✗ tasks - 所有任务记录
-- ✗ task_attachments - 所有任务附件
-- ✗ cash_closings - 所有现金日结记录
-- ✗ consumption_logs - 所有消耗记录
-- ✗ attachments - 所有交易附件
-- ✗ shifts - 所有班次记录
-- ✗ assets - 所有资产记录（如果存在）
-- ✗ materials - 所有材料记录（如果存在）
-- 
-- ============================================

