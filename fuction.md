1) 项目目录结构（原生PHP推荐）
finance-app/
  public/
    index.php              # 单入口
    assets/
      css/app.css
      js/app.js
    uploads/               # 凭证图片
  app/
    config/
      db.php
      app.php
    core/
      Router.php
      DB.php
      Auth.php
      I18n.php
      Csrf.php
      Response.php
    middleware/
      auth.php
    controllers/
      AuthController.php
      TransactionController.php
      ReportController.php
      SettingController.php
    models/
      User.php
      Transaction.php
      Category.php
      PaymentMethod.php
      Vendor.php
    views/
      layout/
        header.php
        footer.php
      auth/
        login.php
      transactions/
        list.php
        create.php
        view.php
      reports/
        dashboard.php
      settings/
        categories.php
        payment_methods.php
  lang/
    zh.php
    vi.php
  storage/
    logs/app.log
  .htaccess (可选)


H5：页面用简单 HTML + Tailwind CDN / 你自己的 CSS；交互用少量 JS（fetch 调 API 或表单提交都行）。

2) 数据库 MySQL（MVP可用：直接建表SQL）

货币默认 VND；金额用 DECIMAL；流水统一表 transactions。

CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  `key` VARCHAR(32) NOT NULL UNIQUE,          -- owner/manager/accountant/staff
  name_zh VARCHAR(64) NOT NULL,
  name_vi VARCHAR(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(64) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  display_name VARCHAR(64) NOT NULL,
  role_id INT NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  `type` ENUM('income','expense','both') NOT NULL,
  name_zh VARCHAR(64) NOT NULL,
  name_vi VARCHAR(64) NOT NULL,
  parent_id INT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT fk_categories_parent FOREIGN KEY (parent_id) REFERENCES categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE payment_methods (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name_zh VARCHAR(64) NOT NULL,
  name_vi VARCHAR(64) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE vendors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(128) NOT NULL,
  phone VARCHAR(32) NULL,
  note VARCHAR(255) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE transactions (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  `type` ENUM('income','expense') NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  currency VARCHAR(8) NOT NULL DEFAULT 'VND',
  category_id INT NOT NULL,
  payment_method_id INT NOT NULL,
  vendor_id INT NULL,
  occurred_at DATETIME NOT NULL,
  note TEXT NULL,
  status ENUM('approved','pending','void') NOT NULL DEFAULT 'approved',
  created_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_tx_category FOREIGN KEY (category_id) REFERENCES categories(id),
  CONSTRAINT fk_tx_payment FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id),
  CONSTRAINT fk_tx_vendor FOREIGN KEY (vendor_id) REFERENCES vendors(id),
  CONSTRAINT fk_tx_user FOREIGN KEY (created_by) REFERENCES users(id),
  INDEX idx_tx_occurred_at (occurred_at),
  INDEX idx_tx_type (type),
  INDEX idx_tx_status (status),
  INDEX idx_tx_category (category_id),
  INDEX idx_tx_payment (payment_method_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE attachments (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  transaction_id BIGINT NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  file_type VARCHAR(32) NULL,
  uploaded_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_att_tx FOREIGN KEY (transaction_id) REFERENCES transactions(id),
  CONSTRAINT fk_att_user FOREIGN KEY (uploaded_by) REFERENCES users(id),
  INDEX idx_att_tx (transaction_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

初始化角色（建议）
INSERT INTO roles(`key`, name_zh, name_vi) VALUES
('owner','老板','Chủ quán'),
('manager','店长','Quản lý'),
('accountant','财务','Kế toán'),
('staff','员工','Nhân viên');

3) 单入口路由（public/index.php 思路）

所有请求走 public/index.php

通过 ?r=transactions/create 这种方式路由（最简单稳定）

例：

/index.php?r=auth/login

/index.php?r=transactions/list

/index.php?r=reports/dashboard

4) 登录与会话（Auth：原生但安全）

password_hash() / password_verify()

Session 保存：user_id, role_key, lang

建议加：

CSRF token（表单提交）

登录失败限速（最简单：按 session 计数）

5) 中越双语 i18n（最省事的方式）
lang/zh.php（示例）
return [
  'app.title' => '店内收支管理',
  'nav.dashboard' => '总览',
  'nav.add' => '记一笔',
  'nav.list' => '流水',
  'login.title' => '登录',
  'tx.income' => '收入',
  'tx.expense' => '支出',
  'field.amount' => '金额',
  'field.category' => '分类',
  'field.payment' => '支付方式',
  'field.vendor' => '供应商',
  'field.time' => '发生时间',
  'field.note' => '备注',
  'btn.save' => '保存',
  'btn.logout' => '退出登录',
];

lang/vi.php（示例）
return [
  'app.title' => 'Quản lý thu chi nội bộ',
  'nav.dashboard' => 'Tổng quan',
  'nav.add' => 'Thêm giao dịch',
  'nav.list' => 'Sổ giao dịch',
  'login.title' => 'Đăng nhập',
  'tx.income' => 'Thu',
  'tx.expense' => 'Chi',
  'field.amount' => 'Số tiền',
  'field.category' => 'Danh mục',
  'field.payment' => 'Phương thức',
  'field.vendor' => 'Nhà cung cấp',
  'field.time' => 'Thời gian',
  'field.note' => 'Ghi chú',
  'btn.save' => 'Lưu',
  'btn.logout' => 'Đăng xuất',
];


页面上用：__('nav.dashboard')。语言切换：?lang=zh / ?lang=vi 写入 session。

6) MVP 页面与功能（原生PHP落地顺序）
P0（立刻能用）

登录页（ZH/VI）

Dashboard：今日收入/支出/净额，本月累计

记一笔：收入/支出表单 + 上传凭证

流水列表：按日期/类型/分类筛选

作废（void）功能（老板/财务）

P1（增强）

报表：按分类饼图、按支付方式汇总

导出 CSV（按筛选条件）