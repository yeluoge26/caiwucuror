# 快速开始指南

## 5分钟快速部署

### 步骤 1: 准备数据库（2分钟）

```bash
# 创建数据库
mysql -u root -p -e "CREATE DATABASE coffee_finance DEFAULT CHARSET=utf8mb4;"

# 导入结构
mysql -u root -p coffee_finance < database/schema.sql
```

### 步骤 2: 配置数据库连接（1分钟）

编辑 `app/config/db.php`，修改数据库密码：

```php
return [
  'host' => '127.0.0.1',
  'dbname' => 'coffee_finance',
  'user' => 'root',
  'pass' => '你的密码',  // 修改这里
  'charset' => 'utf8mb4'
];
```

### 步骤 3: 访问系统（1分钟）

在浏览器打开：
```
http://localhost/index.php?r=auth/login
```

使用默认账号登录：
- 用户名: `admin`
- 密码: `admin123`

### 步骤 4: 开始使用（1分钟）

1. **查看 Dashboard**: 点击"总览"查看统计
2. **添加交易**: 点击"记一笔"录入收入/支出
3. **查看流水**: 点击"流水"查看所有交易记录
4. **管理设置**: 点击"设置"管理分类、支付方式等

## 常用操作

### 录入一笔收入

1. 点击导航栏"记一笔"
2. 选择类型：收入
3. 填写金额、分类、支付方式、时间
4. 点击"保存"

### 录入一笔支出

1. 点击导航栏"记一笔"
2. 选择类型：支出
3. 填写金额、分类、支付方式、供应商、时间
4. 点击"保存"

### 查看统计

1. 点击导航栏"总览"
2. 查看今日/本月收入支出
3. 查看7天趋势
4. 查看最近流水

### 筛选交易

1. 点击导航栏"流水"
2. 使用筛选条件：
   - 类型（收入/支出）
   - 分类
   - 支付方式
   - 日期范围
   - 关键词搜索
3. 点击"搜索"

### 切换语言

点击页面右上角的"中文"或"Tiếng Việt"切换语言

## 默认数据

系统已预置以下数据：

### 分类
- **收入**: 堂食收入、外卖收入、团购收入、其他收入
- **支出**: 食材采购、设备维护、房租水电、员工工资、营销推广、其他支出

### 支付方式
- 现金、POS机刷卡、VNPAY、ZaloPay、Momo

### 角色
- 老板 (Owner)
- 店长 (Manager)
- 财务 (Accountant)
- 员工 (Staff)

## 下一步

- 阅读 [README.md](README.md) 了解完整功能
- 阅读 [INSTALLATION.md](INSTALLATION.md) 了解详细安装步骤
- 阅读 [ARCHITECTURE.md](ARCHITECTURE.md) 了解系统架构

## 常见问题快速解决

**Q: 页面显示 404？**
A: 检查 `.htaccess` 文件是否存在，Apache 是否启用 `mod_rewrite`

**Q: 无法连接数据库？**
A: 检查 `app/config/db.php` 中的数据库配置是否正确

**Q: 登录后跳转失败？**
A: 检查 PHP session 是否正常工作，确保有写入权限

**Q: 中文显示乱码？**
A: 确保数据库使用 `utf8mb4` 字符集，PHP 文件保存为 UTF-8 编码
