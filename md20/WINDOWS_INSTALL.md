# Windows 安装指南

## 快速开始（推荐方式）

### 方式1: 使用 XAMPP（最简单，推荐）

#### 步骤1: 安装 XAMPP

1. **下载 XAMPP**
   - 访问: https://www.apachefriends.org/download.html
   - 下载 XAMPP for Windows（推荐 PHP 8.1 版本）
   - 运行安装程序

2. **安装配置**
   - 选择安装路径（默认: `C:\xampp`）
   - 选择组件：Apache、MySQL、PHP、phpMyAdmin（必须）
   - 完成安装

#### 步骤2: 运行自动配置脚本

1. **以管理员身份运行** `install_windows.bat`
   ```
   右键点击 install_windows.bat → 以管理员身份运行
   ```
   脚本会自动检测环境并指导安装

2. **配置数据库**
   ```
   运行 setup.bat
   ```
   脚本会：
   - 创建数据库 `coffee_finance`
   - 导入数据库结构
   - 创建初始数据

3. **启动服务器**
   ```
   运行 start_server.bat
   ```
   或手动打开 XAMPP Control Panel，启动 Apache 和 MySQL

#### 步骤3: 访问系统

在浏览器中打开：
```
http://localhost/public/index.php?r=auth/login
```

默认登录信息：
- 用户名: `admin`
- 密码: `admin123`

---

### 方式2: 使用 WAMP

#### 步骤1: 安装 WAMP

1. 下载 WAMP: https://www.wampserver.com/
2. 运行安装程序
3. 选择 PHP 8.1+ 版本

#### 步骤2: 配置

1. 运行 `setup.bat` 配置数据库
2. 点击系统托盘 WAMP 图标 → Start All Services

#### 步骤3: 访问

```
http://localhost/public/index.php?r=auth/login
```

---

### 方式3: 使用 Chocolatey（适合开发者）

#### 步骤1: 安装 Chocolatey

以管理员身份运行 PowerShell：
```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force
[System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072
iex ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))
```

#### 步骤2: 安装 XAMPP

```powershell
choco install xampp-81 -y
```

#### 步骤3: 配置

运行 `setup.bat` 和 `start_server.bat`

---

## 手动安装步骤

如果自动脚本无法使用，可以手动安装：

### 1. 安装 XAMPP

1. 访问 https://www.apachefriends.org/download.html
2. 下载并安装 XAMPP
3. 安装到 `C:\xampp`（或自定义路径）

### 2. 启动服务

打开 XAMPP Control Panel：
- 点击 Apache 的 "Start" 按钮
- 点击 MySQL 的 "Start" 按钮

### 3. 创建数据库

打开命令行（CMD）或 PowerShell：

```bash
# 进入项目目录
cd d:\code\caiwucuror

# 使用 XAMPP 的 MySQL
C:\xampp\mysql\bin\mysql.exe -u root

# 在 MySQL 中执行
CREATE DATABASE coffee_finance DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
exit;

# 导入数据库结构
C:\xampp\mysql\bin\mysql.exe -u root coffee_finance < database\schema.sql
```

### 4. 配置数据库连接

编辑 `app/config/db.php`：

```php
return [
  'host' => '127.0.0.1',
  'dbname' => 'coffee_finance',
  'user' => 'root',
  'pass' => '',  // XAMPP 默认 root 密码为空
  'charset' => 'utf8mb4'
];
```

### 5. 配置 Apache

#### 方式A: 使用项目目录作为网站根目录

编辑 `C:\xampp\apache\conf\httpd.conf`，找到：
```apache
DocumentRoot "C:/xampp/htdocs"
<Directory "C:/xampp/htdocs">
```

修改为：
```apache
DocumentRoot "D:/code/caiwucuror/public"
<Directory "D:/code/caiwucuror/public">
```

#### 方式B: 创建虚拟主机（推荐）

编辑 `C:\xampp\apache\conf\extra\httpd-vhosts.conf`，添加：

```apache
<VirtualHost *:80>
    DocumentRoot "D:/code/caiwucuror/public"
    ServerName finance.local
    <Directory "D:/code/caiwucuror/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

编辑 `C:\Windows\System32\drivers\etc\hosts`，添加：
```
127.0.0.1 finance.local
```

然后访问: `http://finance.local/index.php?r=auth/login`

### 6. 重启 Apache

在 XAMPP Control Panel 中重启 Apache

---

## 常见问题

### Q1: 端口被占用（Apache/MySQL 无法启动）

**解决方案：**
1. 检查端口占用：
   ```bash
   netstat -ano | findstr :80
   netstat -ano | findstr :3306
   ```

2. 修改端口（XAMPP Control Panel → Config → httpd.conf）：
   ```apache
   Listen 8080
   ServerName localhost:8080
   ```

3. 然后访问: `http://localhost:8080/public/index.php?r=auth/login`

### Q2: MySQL 无法启动

**解决方案：**
1. 检查 MySQL 日志: `C:\xampp\mysql\data\mysql_error.log`
2. 常见原因：
   - 端口 3306 被占用
   - 数据目录损坏
   - 权限问题

3. 重置 MySQL（会删除所有数据）：
   ```bash
   # 停止 MySQL
   # 删除 C:\xampp\mysql\data 目录（备份重要数据）
   # 重新启动 MySQL
   ```

### Q3: PHP 扩展缺失

**解决方案：**
编辑 `C:\xampp\php\php.ini`，取消注释：
```ini
extension=pdo_mysql
extension=mbstring
extension=mysqli
```

重启 Apache

### Q4: 无法访问 localhost

**解决方案：**
1. 检查 Apache 是否运行
2. 检查防火墙设置
3. 尝试访问 `http://127.0.0.1/public/index.php?r=auth/login`

### Q5: 数据库连接失败

**解决方案：**
1. 检查 MySQL 服务是否运行
2. 检查 `app/config/db.php` 配置
3. 测试连接：
   ```bash
   C:\xampp\mysql\bin\mysql.exe -u root -p
   ```

### Q6: 页面显示 404

**解决方案：**
1. 确认访问路径正确: `public/index.php?r=...`
2. 检查 `.htaccess` 文件是否存在
3. 确认 Apache `mod_rewrite` 已启用（XAMPP 默认已启用）

---

## 测试系统

运行测试脚本：
```
http://localhost/public/test.php
```

或运行：
```bash
C:\xampp\php\php.exe public\test.php
```

---

## 快速命令参考

### 启动/停止服务

**XAMPP:**
- 打开 XAMPP Control Panel
- 点击 Start/Stop 按钮

**命令行（需要管理员权限）:**
```bash
# 启动 Apache
net start Apache2.4

# 停止 Apache
net stop Apache2.4

# 启动 MySQL
net start MySQL

# 停止 MySQL
net stop MySQL
```

### MySQL 命令行

```bash
# 连接 MySQL
C:\xampp\mysql\bin\mysql.exe -u root

# 导入数据库
C:\xampp\mysql\bin\mysql.exe -u root coffee_finance < database\schema.sql

# 导出数据库
C:\xampp\mysql\bin\mysqldump.exe -u root coffee_finance > backup.sql
```

---

## 下一步

安装完成后：
1. ✅ 运行 `setup.bat` 配置数据库
2. ✅ 运行 `start_server.bat` 启动服务
3. ✅ 访问 `http://localhost/public/index.php?r=auth/login`
4. ✅ 使用 `admin/admin123` 登录
5. ✅ 开始使用系统！

如有问题，请查看 `TEST_GUIDE.md` 进行故障排查。
