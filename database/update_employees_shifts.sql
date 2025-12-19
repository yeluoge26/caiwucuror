-- ============================================
-- 员工与班次管理功能 - 数据库更新脚本
-- 适用于已有数据的数据库
-- 
-- 重要提示：
-- 1. ⚠️ 执行前请先备份数据库！
-- 2. 此脚本使用 CREATE TABLE IF NOT EXISTS，不会影响现有数据
-- 3. 如果表已存在，不会报错，也不会覆盖现有数据
-- 4. 可以安全地多次执行
-- ============================================

-- 选择数据库（请根据实际情况修改数据库名）
USE coffee_finance;

-- ============================================
-- 1. 创建员工表
-- ============================================
CREATE TABLE IF NOT EXISTS employees (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(128) NOT NULL COMMENT '姓名',
  role_id INT NOT NULL COMMENT '角色ID',
  phone VARCHAR(32) NULL COMMENT '联系电话',
  email VARCHAR(128) NULL COMMENT '邮箱',
  address TEXT NULL COMMENT '地址',
  status ENUM('active','inactive','resigned') NOT NULL DEFAULT 'active' COMMENT '状态：在职/离职/已辞职',
  hire_date DATE NULL COMMENT '入职时间',
  resign_date DATE NULL COMMENT '离职时间',
  note TEXT NULL COMMENT '备注',
  created_by INT NOT NULL COMMENT '创建人ID',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  CONSTRAINT fk_employees_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT,
  CONSTRAINT fk_employees_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
  INDEX idx_employees_status (status),
  INDEX idx_employees_role (role_id),
  INDEX idx_employees_hire_date (hire_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='员工表';

-- ============================================
-- 2. 创建班次表
-- ============================================
CREATE TABLE IF NOT EXISTS shifts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  shift_date DATE NOT NULL COMMENT '日期',
  shift_type ENUM('morning','afternoon','evening') NOT NULL COMMENT '班次类型：早/中/晚',
  employee_id INT NOT NULL COMMENT '员工ID',
  manager_id INT NULL COMMENT '负责人ID（班次负责人）',
  is_confirmed TINYINT(1) NOT NULL DEFAULT 0 COMMENT '实际到岗确认：0=未确认，1=已到岗',
  confirmed_at DATETIME NULL COMMENT '确认时间',
  confirmed_by INT NULL COMMENT '确认人ID',
  note TEXT NULL COMMENT '备注',
  created_by INT NOT NULL COMMENT '创建人ID',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  CONSTRAINT fk_shifts_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE RESTRICT,
  CONSTRAINT fk_shifts_manager FOREIGN KEY (manager_id) REFERENCES employees(id) ON DELETE SET NULL,
  CONSTRAINT fk_shifts_confirmed_by FOREIGN KEY (confirmed_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_shifts_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
  INDEX idx_shifts_date (shift_date),
  INDEX idx_shifts_type (shift_type),
  INDEX idx_shifts_employee (employee_id),
  INDEX idx_shifts_confirmed (is_confirmed),
  UNIQUE KEY uk_shifts_date_employee_type (shift_date, employee_id, shift_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='班次表';

-- ============================================
-- 执行完成！
-- 
-- 验证命令（可选，用于检查表是否创建成功）：
-- SHOW TABLES LIKE 'employees';
-- SHOW TABLES LIKE 'shifts';
-- DESC employees;
-- DESC shifts;
-- 
-- 如果表已存在，执行此脚本不会报错，也不会影响现有数据
-- ============================================

