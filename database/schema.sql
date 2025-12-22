-- ============================================
-- 财务管理系统数据库结构（完整版）
-- 最后更新：2024年12月
-- 包含所有功能模块的数据表
-- ============================================

-- 创建数据库（如果不存在）
CREATE DATABASE IF NOT EXISTS coffee_finance DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
USE coffee_finance;

-- ============================================
-- 1. 基础系统表
-- ============================================

-- 角色表
CREATE TABLE IF NOT EXISTS roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  `key` VARCHAR(32) NOT NULL UNIQUE COMMENT '角色标识：owner/manager/accountant/staff',
  name_zh VARCHAR(64) NOT NULL COMMENT '中文名称',
  name_vi VARCHAR(64) NOT NULL COMMENT '越南语名称',
  INDEX idx_key (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='角色表';

-- 用户表
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(64) NOT NULL UNIQUE COMMENT '用户名',
  password_hash VARCHAR(255) NOT NULL COMMENT '密码哈希',
  display_name VARCHAR(64) NOT NULL COMMENT '显示名称',
  role_id INT NOT NULL COMMENT '角色ID',
  is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT '是否激活',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT,
  INDEX idx_username (username),
  INDEX idx_role (role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户表';

-- ============================================
-- 2. 财务交易相关表
-- ============================================

-- 分类表
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  `type` ENUM('income','expense','both') NOT NULL COMMENT '类型：收入/支出/两者',
  name_zh VARCHAR(64) NOT NULL COMMENT '中文名称',
  name_vi VARCHAR(64) NOT NULL COMMENT '越南语名称',
  parent_id INT NULL COMMENT '父分类ID（二级分类）',
  is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT '是否激活',
  CONSTRAINT fk_categories_parent FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
  INDEX idx_type (`type`),
  INDEX idx_parent (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='分类表';

-- 支付方式表
CREATE TABLE IF NOT EXISTS payment_methods (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name_zh VARCHAR(64) NOT NULL COMMENT '中文名称',
  name_vi VARCHAR(64) NOT NULL COMMENT '越南语名称',
  is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT '是否激活'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='支付方式表';

-- 供应商表
CREATE TABLE IF NOT EXISTS vendors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(128) NOT NULL COMMENT '供应商名称',
  phone VARCHAR(32) NULL COMMENT '电话',
  note VARCHAR(255) NULL COMMENT '备注',
  is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT '是否激活',
  INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='供应商表';

-- 交易表（核心表）
CREATE TABLE IF NOT EXISTS transactions (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  serial_number VARCHAR(32) NOT NULL COMMENT '流水号：日期-类别-ID',
  `type` ENUM('income','expense') NOT NULL COMMENT '类型：收入/支出',
  amount DECIMAL(12,2) NOT NULL COMMENT '金额',
  currency VARCHAR(8) NOT NULL DEFAULT 'VND' COMMENT '货币',
  category_id INT NOT NULL COMMENT '分类ID',
  payment_method_id INT NOT NULL COMMENT '支付方式ID',
  vendor_id INT NULL COMMENT '供应商ID（支出时使用）',
  payer VARCHAR(128) NULL COMMENT '付款人（支出时使用）',
  occurred_at DATETIME NOT NULL COMMENT '发生时间',
  note TEXT NULL COMMENT '备注',
  status ENUM('approved','pending','void') NOT NULL DEFAULT 'approved' COMMENT '状态：已审核/待审核/已作废',
  created_by INT NOT NULL COMMENT '创建人ID',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  CONSTRAINT fk_tx_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
  CONSTRAINT fk_tx_payment FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id) ON DELETE RESTRICT,
  CONSTRAINT fk_tx_vendor FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE SET NULL,
  CONSTRAINT fk_tx_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
  INDEX idx_tx_serial_number (serial_number),
  INDEX idx_tx_occurred_at (occurred_at),
  INDEX idx_tx_type (`type`),
  INDEX idx_tx_status (status),
  INDEX idx_tx_category (category_id),
  INDEX idx_tx_payment (payment_method_id),
  INDEX idx_tx_created_by (created_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='交易表';

-- 交易附件表
CREATE TABLE IF NOT EXISTS attachments (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  transaction_id BIGINT NOT NULL COMMENT '交易ID',
  file_path VARCHAR(255) NOT NULL COMMENT '文件路径',
  file_type VARCHAR(32) NULL COMMENT '文件类型',
  uploaded_by INT NOT NULL COMMENT '上传人ID',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  CONSTRAINT fk_att_tx FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
  CONSTRAINT fk_att_user FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE RESTRICT,
  INDEX idx_att_tx (transaction_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='交易附件表';

-- 作废申请记录
CREATE TABLE IF NOT EXISTS void_requests (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  transaction_id BIGINT NOT NULL COMMENT '交易ID',
  requested_by INT NOT NULL COMMENT '申请人ID',
  reason VARCHAR(255) NULL COMMENT '申请理由',
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending' COMMENT '状态',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  CONSTRAINT fk_vr_tx FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
  CONSTRAINT fk_vr_user FOREIGN KEY (requested_by) REFERENCES users(id) ON DELETE RESTRICT,
  INDEX idx_vr_tx (transaction_id),
  INDEX idx_vr_user (requested_by),
  INDEX idx_vr_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='作废申请';

-- ============================================
-- 3. 现金日结表（店长管理系统新增）
-- ============================================

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

-- ============================================
-- 4. 资产管理相关表
-- ============================================

-- 固定资产表
CREATE TABLE IF NOT EXISTS assets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category VARCHAR(64) NOT NULL COMMENT '店面：咖啡店/办公/威士忌吧',
  subcategory VARCHAR(128) NULL COMMENT '品类',
  name VARCHAR(128) NULL COMMENT '名称（可空）',
  floor VARCHAR(32) NULL COMMENT '所在楼层',
  location VARCHAR(128) NULL COMMENT '位置描述',
  price DECIMAL(12,2) NOT NULL DEFAULT 0 COMMENT '价格',
  quantity DECIMAL(12,2) NOT NULL DEFAULT 0 COMMENT '数量/库存',
  acquired_at DATE NOT NULL COMMENT '入库时间',
  note VARCHAR(255) NULL COMMENT '备注',
  status ENUM('active','void') NOT NULL DEFAULT 'active' COMMENT '状态',
  created_by INT NOT NULL COMMENT '创建人',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  CONSTRAINT fk_assets_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
  INDEX idx_assets_category (category),
  INDEX idx_assets_floor (floor),
  INDEX idx_assets_acquired (acquired_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='固定资产';

-- 固定资产图片
CREATE TABLE IF NOT EXISTS asset_attachments (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  asset_id INT NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  file_type VARCHAR(32) NULL,
  uploaded_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_asset_att FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE,
  CONSTRAINT fk_asset_att_user FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE RESTRICT,
  INDEX idx_asset_att_asset (asset_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='固定资产图片';

-- ============================================
-- 5. 材料库存相关表
-- ============================================

-- 原料库存表
CREATE TABLE IF NOT EXISTS materials (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(128) NOT NULL COMMENT '原料名称',
  category VARCHAR(64) NOT NULL COMMENT '类别：咖啡豆/牛奶/糖浆粉/耗材等',
  store VARCHAR(32) NOT NULL DEFAULT 'coffee' COMMENT '店面：coffee/office/whiskey',
  unit VARCHAR(16) NOT NULL COMMENT '单位：g/kg/L/个等',
  quantity DECIMAL(12,2) NOT NULL DEFAULT 0 COMMENT '当前库存',
  min_quantity DECIMAL(12,2) NOT NULL DEFAULT 0 COMMENT '安全库存',
  note VARCHAR(255) NULL,
  created_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_materials_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
  INDEX idx_materials_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='原料库存';

-- 原料附件表
CREATE TABLE IF NOT EXISTS material_attachments (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  material_id INT NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  file_type VARCHAR(32) NULL,
  uploaded_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_mat_att FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE CASCADE,
  CONSTRAINT fk_mat_att_user FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE RESTRICT,
  INDEX idx_mat_att (material_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='原料附件';

-- ============================================
-- 6. 饮品管理相关表
-- ============================================

-- 饮品表
CREATE TABLE IF NOT EXISTS drinks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(128) NOT NULL,
  store VARCHAR(32) NOT NULL DEFAULT 'coffee',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='饮品';

-- 饮品配方表
CREATE TABLE IF NOT EXISTS drink_recipes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  drink_id INT NOT NULL,
  material_id INT NOT NULL,
  amount DECIMAL(12,2) NOT NULL COMMENT '每杯耗用量（与原料单位一致）',
  CONSTRAINT fk_drink_recipe_drink FOREIGN KEY (drink_id) REFERENCES drinks(id) ON DELETE CASCADE,
  CONSTRAINT fk_drink_recipe_material FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE RESTRICT,
  UNIQUE KEY uk_drink_material (drink_id, material_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='饮品配方';

-- 耗材消耗记录表
CREATE TABLE IF NOT EXISTS consumption_logs (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  drink_id INT NOT NULL,
  quantity INT NOT NULL COMMENT '饮品数量',
  occurred_at DATE NOT NULL,
  note VARCHAR(255) NULL,
  created_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_consume_drink FOREIGN KEY (drink_id) REFERENCES drinks(id) ON DELETE RESTRICT,
  CONSTRAINT fk_consume_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
  INDEX idx_consume_date (occurred_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='耗材消耗记录';

-- ============================================
-- 7. 巡店检查相关表
-- ============================================

-- 巡店记录表
CREATE TABLE IF NOT EXISTS inspections (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  store VARCHAR(32) NOT NULL DEFAULT 'coffee',
  floor VARCHAR(16) NOT NULL COMMENT '1F/2F/3F/4F',
  visit_no TINYINT NOT NULL DEFAULT 1 COMMENT '1=首次/2=复巡',
  room VARCHAR(64) NOT NULL DEFAULT 'general' COMMENT '卫生间/店面/楼梯等',
  status ENUM('ok','issue') NOT NULL DEFAULT 'ok',
  note VARCHAR(255) NULL,
  reviewed_status ENUM('pending','confirmed','rejected') NOT NULL DEFAULT 'pending' COMMENT '审核状态',
  review_note VARCHAR(255) NULL COMMENT '审核意见',
  reviewed_by INT NULL COMMENT '审核人ID',
  reviewed_at DATETIME NULL COMMENT '审核时间',
  created_by INT NOT NULL COMMENT '创建人ID',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  spot_date DATE NOT NULL COMMENT '巡店日期',
  CONSTRAINT fk_inspect_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
  CONSTRAINT fk_inspect_reviewer FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_inspect_date (spot_date),
  INDEX idx_inspect_floor (floor),
  INDEX idx_inspect_room (room),
  INDEX idx_inspect_visit (visit_no)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='巡店检查';

-- 巡店照片表
CREATE TABLE IF NOT EXISTS inspection_photos (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  inspection_id BIGINT NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  file_type VARCHAR(32) NULL,
  uploaded_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_insp_photo FOREIGN KEY (inspection_id) REFERENCES inspections(id) ON DELETE CASCADE,
  CONSTRAINT fk_insp_photo_user FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE RESTRICT,
  INDEX idx_insp_photo (inspection_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='巡店照片';

-- ============================================
-- 8. 任务管理相关表
-- ============================================

-- 任务表
CREATE TABLE IF NOT EXISTS tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL COMMENT '任务标题',
  `type` ENUM('fixed','temporary') NOT NULL DEFAULT 'temporary' COMMENT '任务类型：固定/临时',
  description TEXT NULL COMMENT '描述说明',
  due_date DATETIME NULL COMMENT '截止时间',
  store VARCHAR(64) NULL COMMENT '关联门店/区域（未来可扩展）',
  assign_type ENUM('role','user') NOT NULL DEFAULT 'user' COMMENT '指派类型：角色/用户',
  assign_role_id INT NULL COMMENT '指派角色ID',
  assign_user_id INT NULL COMMENT '指派用户ID',
  require_photo TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否需要拍照',
  require_attachment TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否需要上传附件',
  penalty_amount DECIMAL(12,2) NULL COMMENT '罚款金额（可选）',
  reward_amount DECIMAL(12,2) NULL COMMENT '奖励金额（可选）',
  currency VARCHAR(8) NOT NULL DEFAULT 'VND' COMMENT '货币',
  status ENUM('pending','in_progress','completed','approved','rejected') NOT NULL DEFAULT 'pending' COMMENT '状态：待处理/进行中/已完成/已审批/已拒绝',
  completed_at DATETIME NULL COMMENT '完成时间',
  completed_by INT NULL COMMENT '完成人ID',
  completion_note TEXT NULL COMMENT '完成说明',
  approved_at DATETIME NULL COMMENT '审批时间',
  approved_by INT NULL COMMENT '审批人ID',
  approval_note TEXT NULL COMMENT '审批说明',
  created_by INT NOT NULL COMMENT '创建人ID',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  CONSTRAINT fk_tasks_assign_role FOREIGN KEY (assign_role_id) REFERENCES roles(id) ON DELETE SET NULL,
  CONSTRAINT fk_tasks_assign_user FOREIGN KEY (assign_user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_tasks_completed_by FOREIGN KEY (completed_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_tasks_approved_by FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_tasks_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
  INDEX idx_tasks_type (`type`),
  INDEX idx_tasks_status (status),
  INDEX idx_tasks_due_date (due_date),
  INDEX idx_tasks_assign_role (assign_role_id),
  INDEX idx_tasks_assign_user (assign_user_id),
  INDEX idx_tasks_store (store)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='任务表';

-- 任务附件表
CREATE TABLE IF NOT EXISTS task_attachments (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  task_id INT NOT NULL COMMENT '任务ID',
  file_path VARCHAR(255) NOT NULL COMMENT '文件路径',
  file_type VARCHAR(32) NULL COMMENT '文件类型',
  uploaded_by INT NOT NULL COMMENT '上传人ID',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  CONSTRAINT fk_task_att_task FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
  CONSTRAINT fk_task_att_user FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE RESTRICT,
  INDEX idx_task_att_task (task_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='任务附件表';

-- ============================================
-- 9. 员工和班次管理相关表
-- ============================================

-- 员工表
CREATE TABLE IF NOT EXISTS employees (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(128) NOT NULL COMMENT '姓名',
  role_id INT NOT NULL COMMENT '角色ID',
  phone VARCHAR(32) NULL COMMENT '联系电话',
  email VARCHAR(128) NULL COMMENT '邮箱',
  address TEXT NULL COMMENT '地址',
  status ENUM('active','inactive','resigned') NOT NULL DEFAULT 'active' COMMENT '状态：在职/离职/已辞职',
  employment_type ENUM('full_time','part_time') NOT NULL DEFAULT 'full_time' COMMENT '雇佣类型：全职/兼职',
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

-- 班次表
CREATE TABLE IF NOT EXISTS shifts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  shift_date DATE NOT NULL COMMENT '日期',
  shift_type ENUM('morning','afternoon','evening') NOT NULL COMMENT '班次类型：早/中/晚',
  employee_id INT NOT NULL COMMENT '员工ID',
  manager_id INT NULL COMMENT '负责人ID（班次负责人，可为空）',
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
-- 10. 初始化数据
-- ============================================

-- 初始化角色数据
INSERT INTO roles(`key`, name_zh, name_vi) VALUES
('owner','老板','Chủ quán'),
('manager','店长','Quản lý'),
('accountant','财务','Kế toán'),
('staff','员工','Nhân viên')
ON DUPLICATE KEY UPDATE name_zh=VALUES(name_zh), name_vi=VALUES(name_vi);

-- 初始化默认用户（密码：admin123）
-- 注意：实际使用时请修改密码
-- 使用正确的 password_hash 生成：password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO users(username, password_hash, display_name, role_id) VALUES
('admin', '$2y$12$cKipuCZmfNWohh5ZSFdWCuQhs/5tzzjbVL.g/lFeicgEn8tYEhHHS', '管理员', 1)
ON DUPLICATE KEY UPDATE display_name=VALUES(display_name);

-- 初始化分类数据
INSERT INTO categories(`type`, name_zh, name_vi) VALUES
-- 收入分类
('income', '堂食收入', 'Doanh thu tại chỗ'),
('income', '外卖收入', 'Doanh thu giao hàng'),
('income', '团购收入', 'Doanh thu nhóm'),
('income', '其他收入', 'Thu nhập khác'),
-- 支出分类
('expense', '食材采购', 'Mua nguyên liệu'),
('expense', '设备维护', 'Bảo trì thiết bị'),
('expense', '房租水电', 'Tiền thuê nhà và điện nước'),
('expense', '员工工资', 'Lương nhân viên'),
('expense', '营销推广', 'Marketing và quảng cáo'),
('expense', '其他支出', 'Chi phí khác')
ON DUPLICATE KEY UPDATE name_zh=VALUES(name_zh), name_vi=VALUES(name_vi);

-- 初始化支付方式
INSERT INTO payment_methods(name_zh, name_vi) VALUES
('现金', 'Tiền mặt'),
('POS机刷卡', 'Quẹt thẻ POS'),
('VNPAY', 'VNPAY'),
('ZaloPay', 'ZaloPay'),
('Momo', 'Momo')
ON DUPLICATE KEY UPDATE name_zh=VALUES(name_zh), name_vi=VALUES(name_vi);

-- 初始化默认供应商
INSERT INTO vendors(name, phone, note, is_active) VALUES
('食材供应商A', '0123456789', '主要食材供应商', 1),
('设备维修公司', '0987654321', '设备维护和维修', 1),
('水电公司', '0111222333', '水电费缴纳', 1),
('广告公司', '0444555666', '营销推广服务', 1),
('其他供应商', NULL, '其他支出供应商', 1)
ON DUPLICATE KEY UPDATE name=VALUES(name), phone=VALUES(phone), note=VALUES(note);

-- ============================================
-- 数据库结构创建完成
-- ============================================
-- 
-- 包含的表：
-- 1. 基础系统：roles, users
-- 2. 财务交易：categories, payment_methods, vendors, transactions, attachments, void_requests
-- 3. 现金日结：cash_closings
-- 4. 资产管理：assets, asset_attachments
-- 5. 材料库存：materials, material_attachments
-- 6. 饮品管理：drinks, drink_recipes, consumption_logs
-- 7. 巡店检查：inspections, inspection_photos
-- 8. 任务管理：tasks, task_attachments
-- 9. 员工班次：employees, shifts
-- 
-- 总计：20个数据表
-- ============================================
