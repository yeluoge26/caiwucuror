# 图片上传权限修复指南

## 问题诊断

根据诊断结果，目录权限存在问题：
- 所有目录的权限都是 `0755`，但**不可写**
- 这导致 `move_uploaded_file()` 失败，出现 "Permission denied" 错误

## 解决方案

### 方法1：修改目录权限（推荐）

在服务器上执行以下命令，将目录权限设置为可写：

```bash
# 进入项目目录
cd /www/wwwroot/test.TECHCOFFEEDANANG.COM/caiwucuror

# 设置 uploads 目录及其子目录为可写（755 或 775）
chmod -R 755 public/uploads
# 或者使用 775（组可写）
chmod -R 775 public/uploads

# 确保 inspections 目录可写
chmod 755 public/uploads/inspections
# 或
chmod 775 public/uploads/inspections
```

### 方法2：修改目录所有者（如果方法1不行）

如果修改权限后仍然无法写入，可能需要修改目录所有者：

```bash
# 查看当前 PHP 运行用户（通常是 www-data 或 nginx）
ps aux | grep php-fpm
# 或
ps aux | grep nginx

# 假设 PHP 运行用户是 www-data，修改目录所有者
chown -R www-data:www-data public/uploads

# 然后设置权限
chmod -R 755 public/uploads
```

### 方法3：使用 777 权限（不推荐，仅临时测试）

**注意：777 权限不安全，仅用于测试，生产环境不推荐使用**

```bash
chmod -R 777 public/uploads
```

## 验证修复

修复后，请：
1. 刷新诊断页面 `check_upload.php`
2. 检查"目录权限检查"部分，应该显示"可写 ✅"
3. 尝试上传测试文件，应该成功

## 常见问题

### Q: 为什么目录权限是 755 但不可写？
A: 755 权限表示：
- 所有者：读、写、执行 (7)
- 组：读、执行 (5)
- 其他：读、执行 (5)

如果 PHP 进程不是目录所有者，且不在组内，则无法写入。需要：
- 确保 PHP 进程用户是目录所有者，或
- 将目录权限改为 775（组可写），并将 PHP 进程用户加入组

### Q: 如何查看 PHP 运行用户？
A: 在 PHP 文件中添加：
```php
<?php
echo "PHP 运行用户: " . get_current_user() . "\n";
echo "进程用户: " . (function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] : 'unknown') . "\n";
```

### Q: 修改权限后仍然无法写入？
A: 检查：
1. SELinux 是否启用（如果启用，可能需要设置上下文）
2. 磁盘空间是否充足
3. 父目录权限是否正确
4. 是否使用了正确的用户执行命令

## 安全建议

1. **不要使用 777 权限**：这会允许所有用户写入，存在安全风险
2. **使用最小权限原则**：只给必要的目录设置写权限
3. **定期检查权限**：确保没有意外修改为过于宽松的权限
4. **使用专用用户**：为 Web 应用创建专用用户，而不是使用 root

