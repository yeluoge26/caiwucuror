-- ============================================
-- 安全清理测试数据脚本（带确认和备份提示）
-- 用途：删除所有测试数据，准备部署到生产环境
-- 警告：此脚本会删除所有业务数据，请谨慎使用！
-- ============================================

USE coffee_finance;

-- ============================================
-- 重要提示：执行前请先备份数据库！
-- ============================================
-- 
-- 备份命令示例：
-- mysqldump -u root -p coffee_finance > backup_$(date +%Y%m%d_%H%M%S).sql
-- 
-- 或者使用phpMyAdmin导出功能
-- ============================================

-- ============================================
-- 步骤1：查看当前数据统计（执行前检查）
-- ============================================

SELECT '当前数据统计' AS info;
SELECT 'transactions' AS table_name, COUNT(*) AS count FROM transactions
UNION ALL
SELECT 'inspections', COUNT(*) FROM inspections
UNION ALL
SELECT 'inspection_photos', COUNT(*) FROM inspection_photos
UNION ALL
SELECT 'tasks', COUNT(*) FROM tasks
UNION ALL
SELECT 'cash_closings', COUNT(*) FROM cash_closings
UNION ALL
SELECT 'consumption_logs', COUNT(*) FROM consumption_logs
UNION ALL
SELECT 'attachments', COUNT(*) FROM attachments
UNION ALL
SELECT 'shifts', COUNT(*) FROM shifts
UNION ALL
SELECT 'employees', COUNT(*) FROM employees;

-- ============================================
-- 步骤2：删除业务数据（按依赖关系顺序）
-- ============================================

-- 2.1 删除附件数据（先删除附件，因为外键约束）
DELETE FROM attachments;
DELETE FROM inspection_photos;
DELETE FROM task_attachments;
DELETE FROM asset_attachments;
DELETE FROM material_attachments;

-- 2.2 删除业务记录
DELETE FROM transactions;
DELETE FROM void_requests;
DELETE FROM inspections;
DELETE FROM tasks;
DELETE FROM cash_closings;
DELETE FROM consumption_logs;

-- 2.3 删除资产和材料记录（如果这些是测试数据）
DELETE FROM assets;
DELETE FROM materials;

-- 2.4 删除班次记录（保留员工表，只删除班次）
DELETE FROM shifts;

-- ============================================
-- 步骤3：清理测试员工数据（请根据实际情况修改）
-- ============================================

-- 选项A：删除所有员工（如果都是测试数据）
-- DELETE FROM employees;

-- 选项B：只删除测试员工（根据insert_sample_employees.sql中的员工名称）
DELETE FROM employees WHERE name IN (
  '张三', '李四', '王五', '赵六', '钱七', 
  '孙八', '周九', '吴十', '郑十一', '王十二'
) OR note LIKE '%虚拟数据%';

-- 选项C：保留所有员工（不执行删除）

-- ============================================
-- 步骤4：重置自增ID（可选）
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
-- 步骤5：验证清理结果
-- ============================================

SELECT '清理后数据统计' AS info;
SELECT 'transactions' AS table_name, COUNT(*) AS count FROM transactions
UNION ALL
SELECT 'inspections', COUNT(*) FROM inspections
UNION ALL
SELECT 'inspection_photos', COUNT(*) FROM inspection_photos
UNION ALL
SELECT 'tasks', COUNT(*) FROM tasks
UNION ALL
SELECT 'cash_closings', COUNT(*) FROM cash_closings
UNION ALL
SELECT 'consumption_logs', COUNT(*) FROM consumption_logs
UNION ALL
SELECT 'attachments', COUNT(*) FROM attachments
UNION ALL
SELECT 'shifts', COUNT(*) FROM shifts;

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
-- 下一步：手动清理上传的文件
-- ============================================
-- 
-- 在服务器上执行以下命令清理上传的文件：
-- 
-- cd /www/wwwroot/test.TECHCOFFEEDANANG.COM/caiwucuror
-- rm -rf public/uploads/*
-- mkdir -p public/uploads/inspections
-- chmod 755 public/uploads public/uploads/inspections
-- 
-- ============================================

