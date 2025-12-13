-- 财务管理系统数据库结构
-- 创建数据库（如果不存在）
CREATE DATABASE IF NOT EXISTS coffee_finance DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
USE coffee_finance;

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
  `type` ENUM('income','expense') NOT NULL COMMENT '类型：收入/支出',
  amount DECIMAL(12,2) NOT NULL COMMENT '金额',
  currency VARCHAR(8) NOT NULL DEFAULT 'VND' COMMENT '货币',
  category_id INT NOT NULL COMMENT '分类ID',
  payment_method_id INT NOT NULL COMMENT '支付方式ID',
  vendor_id INT NULL COMMENT '供应商ID（支出时使用）',
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
  INDEX idx_tx_occurred_at (occurred_at),
  INDEX idx_tx_type (`type`),
  INDEX idx_tx_status (status),
  INDEX idx_tx_category (category_id),
  INDEX idx_tx_payment (payment_method_id),
  INDEX idx_tx_created_by (created_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='交易表';

-- 附件表（可选）
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='附件表';

-- 初始化角色数据
INSERT INTO roles(`key`, name_zh, name_vi) VALUES
('owner','老板','Chủ quán'),
('manager','店长','Quản lý'),
('accountant','财务','Kế toán'),
('staff','员工','Nhân viên')
ON DUPLICATE KEY UPDATE name_zh=VALUES(name_zh), name_vi=VALUES(name_vi);

-- 初始化默认用户（密码：admin123）
-- 注意：实际使用时请修改密码
INSERT INTO users(username, password_hash, display_name, role_id) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '管理员', 1)
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
('银行转账', 'Chuyển khoản ngân hàng'),
('VNPAY', 'VNPAY'),
('ZaloPay', 'ZaloPay'),
('Momo', 'Momo'),
('QR码', 'QR Code')
ON DUPLICATE KEY UPDATE name_zh=VALUES(name_zh), name_vi=VALUES(name_vi);

