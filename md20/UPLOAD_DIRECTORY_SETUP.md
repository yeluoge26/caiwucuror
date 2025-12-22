# 上传目录设置说明

## 问题描述

如果遇到 `mkdir(): Permission denied` 错误，说明上传目录不存在或没有写入权限。

## 解决方案

### 方法1：手动创建目录（推荐）

在服务器上通过 SSH 或 FTP 手动创建上传目录：

```bash
# 进入项目根目录
cd /www/wwwroot/test.TECHCOFFEEDANANG.COM/caiwucuror

# 创建上传目录
mkdir -p public/uploads/inspections
mkdir -p public/uploads/assets
mkdir -p public/uploads/tasks
mkdir -p public/uploads/materials

# 设置目录权限（根据服务器配置调整）
chmod 755 public/uploads
chmod 755 public/uploads/inspections
chmod 755 public/uploads/assets
chmod 755 public/uploads/tasks
chmod 755 public/uploads/materials
```

### 方法2：使用更宽松的权限（如果方法1不行）

```bash
chmod 777 public/uploads
chmod 777 public/uploads/inspections
chmod 777 public/uploads/assets
chmod 777 public/uploads/tasks
chmod 777 public/uploads/materials
```

**注意**：777 权限较为宽松，建议在生产环境中使用 755 或 750。

### 方法3：检查目录所有者

确保目录所有者是 Web 服务器用户（通常是 `www-data`、`nginx` 或 `apache`）：

```bash
# 查看当前目录所有者
ls -la public/uploads

# 修改所有者（根据实际情况调整用户）
chown -R www-data:www-data public/uploads
# 或
chown -R nginx:nginx public/uploads
```

### 方法4：通过 FTP/文件管理器创建

1. 登录 FTP 或文件管理器
2. 导航到 `public/uploads/` 目录
3. 创建以下子目录：
   - `inspections`
   - `assets`
   - `tasks`
   - `materials`
4. 设置目录权限为 755 或 777

## 验证

创建目录后，可以通过以下方式验证：

1. **检查目录是否存在**：
   ```bash
   ls -la public/uploads/
   ```

2. **检查目录权限**：
   ```bash
   ls -ld public/uploads/inspections
   ```

3. **测试写入权限**：
   ```bash
   touch public/uploads/inspections/test.txt
   rm public/uploads/inspections/test.txt
   ```

## 常见问题

### Q: 为什么会出现权限错误？

A: 在服务器环境中，PHP 进程可能没有创建目录的权限。手动创建目录并设置正确的权限可以解决这个问题。

### Q: 应该使用什么权限？

A: 
- **755**：推荐用于生产环境，所有者可读写执行，组和其他用户可读执行
- **777**：最宽松的权限，所有用户都可读写执行，仅在必要时使用

### Q: 如何找到 Web 服务器用户？

A: 
```bash
# Apache
ps aux | grep apache

# Nginx
ps aux | grep nginx

# 或查看 PHP 配置
php -i | grep user
```

## 目录结构

上传目录的完整结构应该是：

```
public/
└── uploads/
    ├── inspections/    # 巡店照片
    ├── assets/         # 资产附件
    ├── tasks/          # 任务附件
    └── materials/      # 物料附件
```

## 安全建议

1. 确保 `public/uploads/` 目录有 `.htaccess` 文件限制直接访问敏感文件
2. 定期清理上传的文件
3. 限制上传文件类型和大小
4. 使用适当的文件命名策略避免文件名冲突

