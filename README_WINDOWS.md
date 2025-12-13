# Windows 快速开始指南

## 🚀 5分钟快速安装

### 步骤1: 安装 XAMPP（如果没有）

1. **下载 XAMPP**
   - 访问: https://www.apachefriends.org/download.html
   - 下载 XAMPP for Windows
   - 运行安装程序，选择 Apache、MySQL、PHP

2. **或运行自动检测脚本**
   ```
   右键点击 install_windows.bat → 以管理员身份运行
   ```

### 步骤2: 配置数据库

运行配置脚本：
```
双击运行 setup.bat
```

脚本会自动：
- ✅ 创建数据库
- ✅ 导入表结构
- ✅ 创建初始数据

### 步骤3: 启动服务器

运行启动脚本：
```
双击运行 start_server.bat
```

或手动：
- 打开 XAMPP Control Panel
- 点击 Apache 的 "Start"
- 点击 MySQL 的 "Start"

### 步骤4: 访问系统

打开浏览器访问：
```
http://localhost/public/index.php?r=auth/login
```

登录信息：
- 用户名: `admin`
- 密码: `admin123`

---

## 📋 文件说明

- `install_windows.bat` - 环境检测和安装工具
- `setup.bat` - 数据库配置脚本
- `start_server.bat` - 启动服务器脚本
- `WINDOWS_INSTALL.md` - 详细安装文档

---

## ⚡ 快速命令

```bash
# 1. 检测环境
install_windows.bat

# 2. 配置数据库
setup.bat

# 3. 启动服务器
start_server.bat

# 4. 访问系统
http://localhost/public/index.php?r=auth/login
```

---

## ❓ 遇到问题？

1. **查看详细文档**: `WINDOWS_INSTALL.md`
2. **运行测试**: `http://localhost/public/test.php`
3. **检查服务**: 确保 Apache 和 MySQL 都在运行

---

## 🎯 下一步

- ✅ 登录系统
- ✅ 查看 Dashboard
- ✅ 添加第一笔交易
- ✅ 测试各项功能

祝使用愉快！🎉
