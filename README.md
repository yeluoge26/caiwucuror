# 财务管理系统 (Finance Management System)

一个完整的店内收支管理系统，支持中越双语，适用于咖啡店、餐厅等小型商户。

## ✨ 功能特性

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

## 🚀 快速开始

### Windows 用户（推荐）

1. **双击运行** `快速开始.bat`
2. 按照菜单提示操作：
   - 选择 2：安装 XAMPP（如果没有）
   - 选择 3：配置数据库
   - 选择 4：启动服务器
   - 选择 5：访问系统

详细说明请查看：[README_WINDOWS.md](README_WINDOWS.md)

### Linux/Mac 用户

```bash
# 1. 创建数据库
mysql -u root -p -e "CREATE DATABASE coffee_finance DEFAULT CHARSET=utf8mb4;"

# 2. 导入数据库结构
mysql -u root -p coffee_finance < database/schema.sql

# 3. 配置数据库连接
# 编辑 app/config/db.php

# 4. 访问系统
# http://localhost/public/index.php?r=auth/login
```

详细说明请查看：[INSTALLATION.md](INSTALLATION.md)

## 📋 默认登录信息

- **用户名**: `admin`
- **密码**: `admin123`

⚠️ **首次登录后请立即修改密码！**

## 📁 项目结构

```
finance-app/
├── public/              # Web 入口目录
│   └── index.php       # 入口文件
├── app/                 # 应用程序核心
│   ├── config/         # 配置文件
│   ├── core/           # 核心类
│   ├── controllers/    # 控制器
│   ├── models/         # 数据模型
│   └── views/          # 视图模板
├── lang/               # 语言包
│   ├── zh.php         # 中文
│   └── vi.php         # 越南语
└── database/           # 数据库脚本
    └── schema.sql     # 数据库结构
```

## 🛠️ 技术栈

- **后端**: PHP 7.4+ (原生 PHP，无框架)
- **数据库**: MySQL 5.7+ / MariaDB
- **前端**: HTML5, CSS3, JavaScript (原生)
- **架构**: MVC 模式

## 📚 文档

- [README_WINDOWS.md](README_WINDOWS.md) - Windows 快速开始
- [WINDOWS_INSTALL.md](WINDOWS_INSTALL.md) - Windows 详细安装文档
- [INSTALLATION.md](INSTALLATION.md) - 通用安装指南
- [QUICKSTART.md](QUICKSTART.md) - 快速开始指南
- [TEST_GUIDE.md](TEST_GUIDE.md) - 测试指南
- [ARCHITECTURE.md](ARCHITECTURE.md) - 系统架构说明

## 🔒 安全特性

- SQL 注入防护（PDO 预处理语句）
- XSS 防护（输出转义）
- CSRF 防护（Token 验证）
- 密码加密（password_hash）
- 基于角色的访问控制（RBAC）

## 📝 许可证

MIT License

## 🤝 贡献

欢迎提交 Issue 和 Pull Request！

## 📞 支持

如有问题，请查看文档或提交 Issue。

---

**注意**: 配置文件 `app/config/db.php` 包含敏感信息，已添加到 `.gitignore`。首次部署时请复制 `app/config/db.php.example` 为 `db.php` 并修改配置。
