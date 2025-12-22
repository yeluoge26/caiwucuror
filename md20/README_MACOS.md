# macOS 安装和运行指南

## 快速开始

### 方法一：使用安装脚本（推荐）

1. **运行安装脚本**：
```bash
./setup_macos.sh
```

脚本会自动：
- 检查并安装 PHP（如果未安装）
- 检查并安装 MySQL（如果未安装）
- 创建数据库
- 导入数据库结构
- 创建配置文件

2. **启动服务器**：
```bash
./start_macos.sh
```

3. **访问系统**：
打开浏览器访问：http://localhost:8000/index.php?r=auth/login

### 方法二：手动安装

#### 1. 安装依赖

```bash
# 安装 PHP
brew install php

# 安装 MySQL
brew install mysql

# 启动 MySQL 服务
brew services start mysql
```

#### 2. 创建数据库

```bash
# 创建数据库
mysql -u root -e "CREATE DATABASE coffee_finance DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"

# 导入数据库结构
mysql -u root coffee_finance < database/schema.sql
```

如果 MySQL 有密码：
```bash
mysql -u root -p coffee_finance < database/schema.sql
```

#### 3. 配置数据库连接

编辑 `app/config/db.php`：
```php
return [
  'host' => '127.0.0.1',
  'dbname' => 'coffee_finance',
  'user' => 'root',
  'pass' => '',  // 如果有密码，填写密码
  'charset' => 'utf8mb4'
];
```

#### 4. 启动服务器

```bash
php -S localhost:8000 -t public
```

#### 5. 访问系统

打开浏览器访问：http://localhost:8000/index.php?r=auth/login

## 默认登录信息

- **用户名**: `admin`
- **密码**: `admin123`

⚠️ **首次登录后请立即修改密码！**

## 常用命令

### 启动 MySQL 服务
```bash
brew services start mysql
```

### 停止 MySQL 服务
```bash
brew services stop mysql
```

### 查看 MySQL 服务状态
```bash
brew services list
```

### 重启 MySQL 服务
```bash
brew services restart mysql
```

## 常见问题

### 1. PHP 未找到

如果提示 `php: command not found`，可能需要添加 PHP 到 PATH：

```bash
# 查找 PHP 安装路径
brew --prefix php

# 添加到 PATH（添加到 ~/.zshrc 或 ~/.bash_profile）
echo 'export PATH="/opt/homebrew/opt/php/bin:$PATH"' >> ~/.zshrc
source ~/.zshrc
```

### 2. MySQL 连接失败

**问题**：数据库连接失败

**解决**：
1. 检查 MySQL 服务是否运行：`brew services list`
2. 检查 `app/config/db.php` 中的配置
3. 如果 MySQL 有密码，确保在配置文件中填写正确

### 3. 数据库导入失败

**问题**：导入 schema.sql 失败

**解决**：
```bash
# 先创建数据库
mysql -u root -e "CREATE DATABASE coffee_finance DEFAULT CHARSET=utf8mb4;"

# 再导入
mysql -u root coffee_finance < database/schema.sql
```

### 4. 端口被占用

**问题**：8000 端口已被占用

**解决**：使用其他端口
```bash
php -S localhost:8080 -t public
```

然后访问：http://localhost:8080/index.php?r=auth/login

## 项目结构

```
caiwucuror/
├── app/                    # 应用程序核心
│   ├── config/            # 配置文件
│   ├── core/              # 核心类
│   ├── controllers/       # 控制器
│   ├── models/            # 数据模型
│   └── views/             # 视图模板
├── public/                # Web 入口目录
│   └── index.php         # 入口文件
├── database/              # 数据库脚本
│   └── schema.sql        # 数据库结构
├── lang/                  # 语言包
├── setup_macos.sh        # macOS 安装脚本
└── start_macos.sh        # macOS 启动脚本
```

## 功能说明

详细功能分析请查看：[功能分析报告.md](功能分析报告.md)

## 技术支持

如遇到问题，请检查：
1. PHP 版本：`php -v`（需要 7.4+）
2. MySQL 版本：`mysql --version`（需要 5.7+）
3. PHP 扩展：确保安装了 PDO、PDO_MySQL、mbstring、session

