# 系统架构说明

## 整体架构

本财务管理系统采用 MVC（Model-View-Controller）架构模式，使用原生 PHP 开发，无框架依赖。

```
┌─────────────────┐
│   Web Browser   │
└────────┬────────┘
         │ HTTP Request
         ▼
┌─────────────────┐
│  public/index.php│  ← 入口文件
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Router.php     │  ← 路由分发
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Controller      │  ← 控制器处理业务逻辑
└────────┬────────┘
         │
    ┌────┴────┐
    ▼         ▼
┌────────┐ ┌────────┐
│ Model  │ │  View  │  ← 数据模型和视图
└────┬───┘ └────┬───┘
     │          │
     ▼          ▼
┌────────┐ ┌────────┐
│   DB   │ │  Lang  │  ← 数据库和语言包
└────────┘ └────────┘
```

## 目录结构详解

### public/ - Web 入口目录

- `index.php` - 应用程序入口文件，所有请求都通过此文件处理

### app/ - 应用程序核心

#### app/config/ - 配置文件
- `app.php` - 应用程序配置（名称、默认语言、时区等）
- `db.php` - 数据库连接配置

#### app/core/ - 核心类库
- `DB.php` - 数据库连接和操作封装（PDO）
- `Router.php` - 路由分发器，解析 URL 并调用对应控制器
- `Auth.php` - 身份认证和权限管理
- `I18n.php` - 国际化/多语言支持
- `Csrf.php` - CSRF 防护
- `Response.php` - HTTP 响应处理

#### app/controllers/ - 控制器
- `AuthController.php` - 登录/登出
- `TransactionController.php` - 交易管理（创建、列表、查看、作废）
- `ReportController.php` - 报表统计（Dashboard、汇总、分类统计）
- `SettingController.php` - 设置管理（分类、支付方式、供应商）

#### app/models/ - 数据模型
- `User.php` - 用户模型
- `Transaction.php` - 交易模型
- `Category.php` - 分类模型
- `PaymentMethod.php` - 支付方式模型
- `Vendor.php` - 供应商模型

#### app/views/ - 视图模板
- `layout/` - 布局文件
  - `header.php` - 页面头部（导航、样式）
  - `footer.php` - 页面底部
- `auth/` - 认证相关视图
  - `login.php` - 登录页面
- `transactions/` - 交易相关视图
  - `create.php` - 创建交易
  - `list.php` - 交易列表
  - `view.php` - 交易详情
  - `void.php` - 作废交易
- `reports/` - 报表视图
  - `dashboard.php` - 仪表盘
- `settings/` - 设置视图
  - `categories.php` - 分类管理
  - `payment_methods.php` - 支付方式管理
  - `vendors.php` - 供应商管理

### lang/ - 语言包
- `zh.php` - 中文翻译
- `vi.php` - 越南语翻译

### database/ - 数据库脚本
- `schema.sql` - 数据库结构定义和初始数据

## 数据流

### 1. 请求处理流程

```
用户请求 → index.php → Router::dispatch() → Controller::action() → Model → View → 响应
```

### 2. 认证流程

```
未登录用户 → Auth::requireLogin() → 重定向到登录页
已登录用户 → 检查权限 → 执行操作
```

### 3. 数据操作流程

```
Controller → Model::method() → DB::conn() → PDO → MySQL
```

## 核心功能模块

### 1. 路由系统

路由通过 `?r=controller/action` 参数实现：

```php
// 示例：/index.php?r=transactions/create
// Router 解析为：
// - Controller: TransactionController
// - Action: create
```

### 2. 认证系统

- Session 存储用户信息
- 密码使用 `password_hash()` 加密
- 角色权限控制（RBAC）

### 3. 数据库操作

使用 PDO 进行数据库操作，支持预处理语句防止 SQL 注入：

```php
$stmt = DB::conn()->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
```

### 4. 国际化系统

- 语言切换通过 `?lang=zh` 或 `?lang=vi`
- Session 保存用户选择的语言
- 视图使用 `__('key')` 函数获取翻译

### 5. CSRF 防护

- 表单提交时生成 CSRF token
- 提交时验证 token 防止跨站请求伪造

## 数据库设计

### 核心表关系

```
roles (角色)
  └── users (用户)
        └── transactions (交易)
              ├── categories (分类)
              ├── payment_methods (支付方式)
              └── vendors (供应商)
```

### 主要表结构

1. **roles** - 角色表
2. **users** - 用户表（关联 roles）
3. **categories** - 分类表（支持自关联 parent_id）
4. **payment_methods** - 支付方式表
5. **vendors** - 供应商表
6. **transactions** - 交易表（核心表）
7. **attachments** - 附件表（可选）

## 安全特性

1. **SQL 注入防护**: 使用 PDO 预处理语句
2. **XSS 防护**: 输出时使用 `htmlspecialchars()`
3. **CSRF 防护**: 表单 token 验证
4. **密码加密**: 使用 `password_hash()` 和 `password_verify()`
5. **权限控制**: 基于角色的访问控制（RBAC）

## 扩展指南

### 添加新功能

1. **创建控制器**:
```php
// app/controllers/NewController.php
class NewController {
    public function index() {
        // 处理逻辑
    }
}
```

2. **创建模型**:
```php
// app/models/NewModel.php
class NewModel {
    public static function findAll() {
        // 数据库操作
    }
}
```

3. **创建视图**:
```php
// app/views/new/index.php
<?php include __DIR__ . '/../layout/header.php'; ?>
<!-- 视图内容 -->
<?php include __DIR__ . '/../layout/footer.php'; ?>
```

4. **添加路由**: 通过 `?r=new/index` 访问

### 添加新语言

1. 在 `lang/` 目录创建新语言文件（如 `en.php`）
2. 在 `app/core/I18n.php` 中添加语言支持
3. 在视图中添加语言切换链接

## 性能优化建议

1. **数据库优化**:
   - 为常用查询字段添加索引
   - 使用连接池（生产环境）

2. **缓存策略**:
   - 缓存分类、支付方式等基础数据
   - 使用 Redis 缓存统计结果

3. **代码优化**:
   - 启用 PHP OPcache
   - 合并 CSS/JS 文件
   - 使用 CDN 加速静态资源

## 技术栈总结

- **后端**: PHP 7.4+ (原生 PHP，无框架)
- **数据库**: MySQL 5.7+ / MariaDB
- **前端**: HTML5, CSS3, JavaScript (原生)
- **架构**: MVC 模式
- **安全**: PDO, CSRF Token, Password Hash

