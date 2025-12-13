# 安装和配置指南

## 系统要求

- PHP 7.4 或更高版本
- MySQL 5.7 或更高版本（或 MariaDB 10.2+）
- Apache/Nginx Web 服务器
- 启用 PHP 扩展：PDO, PDO_MySQL, mbstring, session

## 快速安装

### 1. 下载代码

将代码放置到 Web 服务器目录（如 `/var/www/html` 或 `C:\xampp\htdocs`）

### 2. 配置数据库

#### 2.1 创建数据库

```sql
CREATE DATABASE coffee_finance DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 2.2 导入数据库结构

```bash
mysql -u root -p coffee_finance < database/schema.sql
```

或在 MySQL 客户端中执行：
```sql
SOURCE database/schema.sql;
```

#### 2.3 配置数据库连接

编辑文件 `app/config/db.php`：

```php
return [
  'host' => '127.0.0.1',        // 数据库主机
  'dbname' => 'coffee_finance',  // 数据库名
  'user' => 'root',              // 数据库用户名
  'pass' => 'your_password',      // 数据库密码
  'charset' => 'utf8mb4'
];
```

### 3. 配置 Web 服务器

#### Apache 配置

确保 `.htaccess` 文件在项目根目录，并且 Apache 启用了 `mod_rewrite`。

如果 `.htaccess` 不工作，可以修改 Apache 配置：

```apache
<Directory "/path/to/finance-app/public">
    AllowOverride All
    Require all granted
</Directory>
```

#### Nginx 配置

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/finance-app/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?r=$uri&$args;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

### 4. 设置文件权限

确保 Web 服务器有读取权限：

```bash
chmod -R 755 public/
chmod -R 755 app/
chmod -R 755 lang/
```

如果需要上传文件功能，创建上传目录并设置写入权限：

```bash
mkdir -p public/uploads
chmod 777 public/uploads
```

### 5. 访问系统

在浏览器中访问：
```
http://localhost/index.php?r=auth/login
```

或配置虚拟主机后：
```
http://your-domain.com/index.php?r=auth/login
```

### 6. 默认登录信息

- **用户名**: `admin`
- **密码**: `admin123`

**⚠️ 重要提示：首次登录后请立即修改密码！**

## 文件结构说明

### 重要目录

- `public/` - Web 入口目录，包含 `index.php`
- `app/` - 应用程序核心代码
  - `config/` - 配置文件
  - `core/` - 核心类（DB, Auth, Router 等）
  - `controllers/` - 控制器
  - `models/` - 数据模型
  - `views/` - 视图模板
- `lang/` - 语言包文件
- `database/` - 数据库脚本

### 旧文件说明

`public/` 目录下有一些旧版本的文件（如 `app.php`, `db.php`, `router.php` 等），这些文件已被新的 `app/` 目录结构替代，可以安全删除或保留作为备份。

## 常见问题排查

### 问题1: 页面显示 404 错误

**原因**: 路由配置不正确或文件路径错误

**解决**:
1. 检查 `.htaccess` 文件是否存在
2. 确认 Apache `mod_rewrite` 已启用
3. 检查 `app/core/Router.php` 中的路径是否正确

### 问题2: 数据库连接失败

**原因**: 数据库配置错误

**解决**:
1. 检查 `app/config/db.php` 中的配置
2. 确认数据库服务正在运行
3. 测试数据库连接：
```php
<?php
$config = require 'app/config/db.php';
try {
    $pdo = new PDO("mysql:host={$config['host']};dbname={$config['dbname']}", 
                   $config['user'], $config['pass']);
    echo "连接成功";
} catch(PDOException $e) {
    echo "连接失败: " . $e->getMessage();
}
```

### 问题3: Session 无法保存

**原因**: PHP session 目录权限问题

**解决**:
1. 检查 PHP session 目录权限
2. 在 `php.ini` 中配置 session.save_path
3. 确保目录可写：
```bash
chmod 777 /var/lib/php/sessions
```

### 问题4: 中文/越南语显示乱码

**原因**: 字符编码问题

**解决**:
1. 确保数据库使用 `utf8mb4` 字符集
2. 确保 PHP 文件保存为 UTF-8 编码
3. 在数据库连接时设置字符集（已在 `DB.php` 中配置）

### 问题5: 无法登录

**原因**: 用户表数据问题或密码哈希不匹配

**解决**:
1. 检查数据库中是否有用户数据
2. 重置管理员密码：
```sql
UPDATE users SET password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE username = 'admin';
-- 密码: admin123
```

## 生产环境部署建议

1. **安全设置**:
   - 修改默认管理员密码
   - 限制数据库用户权限
   - 启用 HTTPS
   - 配置防火墙规则

2. **性能优化**:
   - 启用 PHP OPcache
   - 配置 MySQL 查询缓存
   - 使用 CDN 加速静态资源

3. **备份策略**:
   - 定期备份数据库
   - 备份上传的文件
   - 保留代码版本控制

4. **监控**:
   - 设置错误日志
   - 监控系统性能
   - 定期检查安全更新

## 技术支持

如遇到问题，请检查：
1. PHP 错误日志
2. Web 服务器错误日志
3. 数据库错误日志

