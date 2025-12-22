# 测试和调试文件清理清单

## 可以删除的测试/调试文件

### 1. 诊断和测试脚本（建议保留但限制访问）
- `public/test.php` - 系统功能测试页面（开发时有用，生产环境应删除或限制访问）
- `public/check_inspection_photos.php` - 巡店照片上传诊断脚本（调试用，生产环境应删除或限制访问）
- `public/check_upload.php` - 图片上传诊断脚本（调试用，生产环境应删除或限制访问）
- `public/view_logs.php` - 错误日志查看器（调试用，生产环境应删除或限制访问）

### 2. 一次性迁移脚本（执行后应删除）
- `public/migrate_cash_closings.php` - 现金日结表迁移脚本（执行后应删除）

### 3. 旧的测试/重复文件（可以删除）
- `public/creat.php` - 旧的创建页面（测试文件，已废弃）
- `public/app.php` - 旧的配置文件（重复文件，实际配置在 app/config/app.php）
- `public/db.php` - 旧的数据库配置（重复文件，实际配置在 app/config/db.php）
- `public/router.php` - 旧的路由类（重复文件，实际代码在 app/core/Router.php）
- `public/transaction.php` - 旧的交易模型（重复文件，实际代码在 app/models/Transaction.php）
- `public/list.php` - 旧的列表页面（测试文件，已废弃）
- `public/TransactionController.php` - 旧的控制器（重复文件，实际代码在 app/controllers/TransactionController.php）
- `public/i18n.php` - 旧的国际化类（重复文件，实际代码在 app/core/I18n.php）

### 4. 文本文件（可以删除）
- `public/auth.txt` - 认证相关文本（测试文件）
- `public/create.txt` - 创建相关文本（测试文件）
- `public/csrf.txt` - CSRF相关文本（测试文件）

### 5. Markdown文档（可以移动到docs目录或删除）
- `public/Dashboard 要展示什么（P0）.md` - 文档文件，不应在public目录

## 建议操作

### 立即删除（已废弃的重复文件）
```bash
# 删除旧的测试和重复文件
rm public/creat.php
rm public/app.php
rm public/db.php
rm public/router.php
rm public/transaction.php
rm public/list.php
rm public/TransactionController.php
rm public/i18n.php
rm public/auth.txt
rm public/create.txt
rm public/csrf.txt
rm public/Dashboard\ 要展示什么（P0）.md
```

### 限制访问或删除（诊断工具）
如果这些诊断工具在生产环境中不再需要，可以删除：
```bash
rm public/test.php
rm public/check_inspection_photos.php
rm public/check_upload.php
rm public/view_logs.php
```

或者添加访问限制（在文件开头添加）：
```php
// 只允许本地访问或特定IP
$allowedIPs = ['127.0.0.1', '::1', 'your-admin-ip'];
if (!in_array($_SERVER['REMOTE_ADDR'], $allowedIPs)) {
    die('Access Denied');
}
```

### 迁移脚本（执行后删除）
```bash
# 如果迁移已完成，删除迁移脚本
rm public/migrate_cash_closings.php
```

## 保留的文件

### 必需文件
- `public/index.php` - 应用入口文件（必需）
- `public/api.php` - API入口文件（如果使用）

### 上传目录
- `public/uploads/` - 上传文件目录（必需，但应检查权限）

## 总结

**可以安全删除的文件（共13个）：**
1. `public/creat.php`
2. `public/app.php`
3. `public/db.php`
4. `public/router.php`
5. `public/transaction.php`
6. `public/list.php`
7. `public/TransactionController.php`
8. `public/i18n.php`
9. `public/auth.txt`
10. `public/create.txt`
11. `public/csrf.txt`
12. `public/Dashboard 要展示什么（P0）.md`
13. `public/migrate_cash_closings.php`（如果迁移已完成）

**建议限制访问或删除的诊断工具（4个）：**
1. `public/test.php`
2. `public/check_inspection_photos.php`
3. `public/check_upload.php`
4. `public/view_logs.php`

