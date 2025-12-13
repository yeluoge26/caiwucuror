# 财务管理系统 (Finance Management System)

一个完整的店内收支管理系统，支持中越双语，适用于咖啡店、餐厅等小型商户。

## 功能特性

- ✅ 收入/支出录入
- ✅ 分类管理（收入/支出分类）
- ✅ 支付方式管理
- ✅ 供应商管理
- ✅ 交易流水列表与筛选
- ✅ Dashboard 统计（今日/本月/7天趋势）
- ✅ 角色权限管理（老板/店长/财务/员工）
- ✅ 中越双语支持（中文/越南语）
- ✅ 交易作废功能
- ✅ 响应式设计（支持移动端）

## 技术栈

- PHP 7.4+
- MySQL 5.7+
- 原生 PHP（无框架依赖）
- 响应式 HTML/CSS

## 目录结构

```
finance-app/
├── public/
│   └── index.php          # 入口文件
├── app/
│   ├── config/            # 配置文件
│   ├── core/              # 核心类
│   ├── controllers/       # 控制器
│   ├── models/            # 模型
│   └── views/             # 视图
├── lang/                  # 语言包
│   ├── zh.php            # 中文
│   └── vi.php            # 越南语
└── database/
    └── schema.sql        # 数据库结构
```

## 安装步骤

### 1. 数据库配置

1. 创建数据库：
```sql
CREATE DATABASE coffee_finance DEFAULT CHARSET=utf8mb4;
```

2. 导入数据库结构：
```bash
mysql -u root -p coffee_finance < database/schema.sql
```

3. 修改数据库配置：
编辑 `app/config/db.php`，修改数据库连接信息：
```php
return [
  'host' => '127.0.0.1',
  'dbname' => 'coffee_finance',
  'user' => 'root',
  'pass' => 'your_password',
  'charset' => 'utf8mb4'
];
```

### 2. Web服务器配置

#### Apache
确保 `.htaccess` 文件存在（如果需要 URL 重写）：
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?r=$1 [QSA,L]
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?r=$uri&$args;
}
```

### 3. 默认登录信息

- 用户名：`admin`
- 密码：`admin123`

**⚠️ 重要：首次登录后请立即修改密码！**

## 使用说明

### 路由规则

所有请求通过 `index.php?r=controller/action` 访问：

- `/index.php?r=auth/login` - 登录页
- `/index.php?r=reports/dashboard` - Dashboard
- `/index.php?r=transactions/create` - 新增交易
- `/index.php?r=transactions/list` - 交易列表
- `/index.php?r=settings/categories` - 分类管理
- `/index.php?r=settings/paymentMethods` - 支付方式管理
- `/index.php?r=settings/vendors` - 供应商管理

### 角色权限

- **老板 (Owner)**: 全权限，可查看所有统计、删除/作废记录
- **店长 (Manager)**: 录入、查看、审批
- **财务 (Accountant)**: 录入、审核、导出、作废
- **员工 (Staff)**: 只能新增记录，不能删除，只能看自己提交的

### 语言切换

页面右上角可切换语言：
- 中文 (ZH)
- Tiếng Việt (VI)

或通过 URL 参数：`?lang=zh` 或 `?lang=vi`

## 开发说明

### 添加新功能

1. **添加控制器**：在 `app/controllers/` 创建新的控制器类
2. **添加模型**：在 `app/models/` 创建新的模型类
3. **添加视图**：在 `app/views/` 创建对应的视图文件
4. **添加路由**：通过 `?r=controller/action` 访问

### 添加新语言

1. 在 `lang/` 目录创建新的语言文件（如 `en.php`）
2. 在 `app/core/I18n.php` 中添加语言支持
3. 在视图中使用 `__('key')` 函数获取翻译

## 常见问题

### Q: 无法连接数据库？
A: 检查 `app/config/db.php` 中的数据库配置是否正确。

### Q: 页面显示乱码？
A: 确保数据库和 PHP 文件都使用 UTF-8 编码。

### Q: 登录后跳转失败？
A: 检查 session 是否正常启动，确保 PHP 有写入 session 的权限。

## 许可证

MIT License

## 更新日志

### v1.0.0 (2024)
- 初始版本发布
- 实现核心功能：交易录入、列表、统计
- 支持中越双语
- 角色权限管理

