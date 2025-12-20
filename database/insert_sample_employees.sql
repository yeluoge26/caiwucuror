-- ============================================
-- 插入10个员工虚拟数据，默认为今日晚班
-- ============================================
-- 执行此脚本前，请确保已执行 add_shift_status.sql 迁移

-- 获取当前日期
SET @today = CURDATE();
SET @manager_role_id = (SELECT id FROM roles WHERE `key` = 'manager' LIMIT 1);
SET @staff_role_id = (SELECT id FROM roles WHERE `key` = 'staff' LIMIT 1);
SET @accountant_role_id = (SELECT id FROM roles WHERE `key` = 'accountant' LIMIT 1);
SET @owner_user_id = (SELECT id FROM users WHERE role_id = (SELECT id FROM roles WHERE `key` = 'owner' LIMIT 1) LIMIT 1);

-- 如果找不到用户，使用第一个用户
SET @creator_id = COALESCE(@owner_user_id, (SELECT id FROM users LIMIT 1), 1);

-- 插入10个员工数据
INSERT INTO employees (name, role_id, phone, email, address, status, employment_type, hire_date, resign_date, note, created_by, created_at, updated_at) VALUES
('张三', @staff_role_id, '13800138001', 'zhangsan@example.com', '北京市朝阳区', 'active', 'full_time', @today, '2099-09-09', '虚拟数据', @creator_id, NOW(), NOW()),
('李四', @staff_role_id, '13800138002', 'lisi@example.com', '北京市海淀区', 'active', 'full_time', @today, '2099-09-09', '虚拟数据', @creator_id, NOW(), NOW()),
('王五', @staff_role_id, '13800138003', 'wangwu@example.com', '北京市西城区', 'active', 'full_time', @today, '2099-09-09', '虚拟数据', @creator_id, NOW(), NOW()),
('赵六', @staff_role_id, '13800138004', 'zhaoliu@example.com', '北京市东城区', 'active', 'full_time', @today, '2099-09-09', '虚拟数据', @creator_id, NOW(), NOW()),
('钱七', @staff_role_id, '13800138005', 'qianqi@example.com', '北京市丰台区', 'active', 'full_time', @today, '2099-09-09', '虚拟数据', @creator_id, NOW(), NOW()),
('孙八', @manager_role_id, '13800138006', 'sunba@example.com', '北京市石景山区', 'active', 'full_time', @today, '2099-09-09', '虚拟数据', @creator_id, NOW(), NOW()),
('周九', @staff_role_id, '13800138007', 'zhoujiu@example.com', '北京市通州区', 'active', 'part_time', @today, '2099-09-09', '虚拟数据', @creator_id, NOW(), NOW()),
('吴十', @staff_role_id, '13800138008', 'wushi@example.com', '北京市昌平区', 'active', 'full_time', @today, '2099-09-09', '虚拟数据', @creator_id, NOW(), NOW()),
('郑十一', @accountant_role_id, '13800138009', 'zhengshiyi@example.com', '北京市大兴区', 'active', 'full_time', @today, '2099-09-09', '虚拟数据', @creator_id, NOW(), NOW()),
('王十二', @staff_role_id, '13800138010', 'wangshier@example.com', '北京市房山区', 'active', 'part_time', @today, '2099-09-09', '虚拟数据', @creator_id, NOW(), NOW());

-- 为每个员工创建今日晚班
-- 注意：需要先插入员工数据，然后才能创建班次
-- 如果已执行 add_shift_status.sql 迁移，使用包含 status 字段的版本
-- 如果未执行迁移，请先执行 add_shift_status.sql，或使用下面的注释版本

-- 版本1：包含 status 字段（推荐，如果已执行迁移）
INSERT INTO shifts (shift_date, shift_type, employee_id, manager_id, status, is_confirmed, note, created_by, created_at, updated_at)
SELECT 
  @today as shift_date,
  'evening' as shift_type,
  e.id as employee_id,
  NULL as manager_id,
  'pending' as status,
  0 as is_confirmed,
  '虚拟数据' as note,
  @creator_id as created_by,
  NOW() as created_at,
  NOW() as updated_at
FROM employees e
WHERE e.name IN ('张三', '李四', '王五', '赵六', '钱七', '孙八', '周九', '吴十', '郑十一', '王十二')
  AND e.created_at >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)
  AND NOT EXISTS (
    SELECT 1 FROM shifts s 
    WHERE s.shift_date = @today 
      AND s.shift_type = 'evening' 
      AND s.employee_id = e.id
  );

-- 版本2：不包含 status 字段（如果未执行迁移，取消上面的注释，注释掉版本1）
-- INSERT INTO shifts (shift_date, shift_type, employee_id, manager_id, is_confirmed, note, created_by, created_at, updated_at)
-- SELECT 
--   @today as shift_date,
--   'evening' as shift_type,
--   e.id as employee_id,
--   NULL as manager_id,
--   0 as is_confirmed,
--   '虚拟数据' as note,
--   @creator_id as created_by,
--   NOW() as created_at,
--   NOW() as updated_at
-- FROM employees e
-- WHERE e.name IN ('张三', '李四', '王五', '赵六', '钱七', '孙八', '周九', '吴十', '郑十一', '王十二')
--   AND e.created_at >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)
--   AND NOT EXISTS (
--     SELECT 1 FROM shifts s 
--     WHERE s.shift_date = @today 
--       AND s.shift_type = 'evening' 
--       AND s.employee_id = e.id
--   );

-- ============================================
-- 执行完成！
-- ============================================
-- 验证命令（可选）：
-- SELECT COUNT(*) as employee_count FROM employees WHERE name IN ('张三', '李四', '王五', '赵六', '钱七', '孙八', '周九', '吴十', '郑十一', '王十二');
-- SELECT COUNT(*) as shift_count FROM shifts WHERE shift_date = CURDATE() AND shift_type = 'evening';
-- SELECT e.name, s.shift_date, s.shift_type, s.status FROM employees e LEFT JOIN shifts s ON e.id = s.employee_id WHERE e.name IN ('张三', '李四', '王五', '赵六', '钱七', '孙八', '周九', '吴十', '郑十一', '王十二') AND s.shift_date = CURDATE();

