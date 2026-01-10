-- 器材租赁表
CREATE TABLE IF NOT EXISTS equipment_rentals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(128) NOT NULL COMMENT '客户姓名',
  equipment_name VARCHAR(128) NOT NULL COMMENT '器材名称',
  equipment_serial VARCHAR(128) NULL COMMENT '器材序列号',
  start_date DATE NOT NULL COMMENT '租赁开始日期',
  end_date DATE NOT NULL COMMENT '租赁结束日期',
  deposit DECIMAL(12,2) NOT NULL DEFAULT 0 COMMENT '押金',
  deposit_payment_method_id INT NULL COMMENT '押金支付方式ID',
  id_card_photo VARCHAR(255) NULL COMMENT '证件照片路径',
  status ENUM('active','returned','cancelled') NOT NULL DEFAULT 'active' COMMENT '状态：进行中/已归还/已取消',
  note VARCHAR(500) NULL COMMENT '备注',
  created_by INT NOT NULL COMMENT '创建人',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  CONSTRAINT fk_equipment_rentals_payment FOREIGN KEY (deposit_payment_method_id) REFERENCES payment_methods(id) ON DELETE SET NULL,
  CONSTRAINT fk_equipment_rentals_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
  INDEX idx_customer_name (customer_name),
  INDEX idx_equipment_name (equipment_name),
  INDEX idx_start_date (start_date),
  INDEX idx_end_date (end_date),
  INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='器材租赁表';
