-- ============================================
-- 任务管理功能数据库更新脚本
-- 适用于已有数据的数据库
-- 执行前请先备份数据库！
-- ============================================

USE coffee_finance;

-- 检查表是否存在，如果不存在则创建
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
-- 执行完成！
-- 如果表已存在，此脚本不会报错，也不会影响现有数据
-- ============================================

