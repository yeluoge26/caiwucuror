# phpMyAdmin 导入数据库指南

## 📋 使用phpMyAdmin导入数据库的详细步骤

### 步骤1：登录phpMyAdmin

1. 打开浏览器，访问phpMyAdmin（通常是 `http://your-domain/phpmyadmin`）
2. 使用数据库管理员账号登录

### 步骤2：创建数据库（如果不存在）

1. 点击左侧的"新建"或"New"按钮
2. 输入数据库名称：`test_finance`（或你配置的数据库名）
3. 选择排序规则：`utf8mb4_unicode_ci`
4. 点击"创建"或"Create"按钮

### 步骤3：选择数据库

1. 在左侧数据库列表中，点击 `test_finance` 数据库
2. 确保已选中该数据库（数据库名称会显示在顶部）

### 步骤4：导入SQL文件

#### 方式A：文件上传导入（推荐）

1. 点击顶部菜单的"导入"或"Import"标签
2. 在"选择文件"或"Choose File"部分：
   - 点击"选择文件"按钮
   - 选择文件：`database/schema_for_phpmyadmin.sql`
   - 或者选择：`database/schema.sql`（会自动处理）
3. 在"格式"或"Format"下拉框中选择：`SQL`
4. 其他设置保持默认：
   - 字符集：`utf8mb4`
   - 部分导入：不勾选
   - 允许中断：可以勾选（如果导入失败可以查看错误）
5. 点击底部的"执行"或"Go"按钮

#### 方式B：SQL命令导入

1. 点击顶部菜单的"SQL"标签
2. 打开 `database/schema_for_phpmyadmin.sql` 文件
3. 复制所有内容
4. 粘贴到SQL命令框中
5. 点击"执行"或"Go"按钮

### 步骤5：验证导入结果

导入完成后，检查：

1. **查看表列表**
   - 在左侧应该看到20个表
   - 如果看到"20个表"，说明导入成功

2. **检查关键表**
   - 点击 `roles` 表，应该看到4条记录（owner, manager, accountant, staff）
   - 点击 `users` 表，应该看到1条记录（admin用户）
   - 点击 `categories` 表，应该看到10条记录
   - 点击 `payment_methods` 表，应该看到5条记录

3. **验证新表**
   - 检查 `cash_closings` 表是否存在
   - 检查 `employees` 表是否有 `employment_type` 字段
   - 检查 `shifts` 表的 `manager_id` 字段是否可为空

### 步骤6：测试登录

导入完成后，访问系统登录页面：
```
http://test.TECHCOFFEEDANANG.COM/caiwucuror/public/index.php?r=auth/login
```

**默认登录信息：**
- 用户名：`admin`
- 密码：`admin123`

## 📁 文件说明

### schema_for_phpmyadmin.sql
- **用途**：专门为phpMyAdmin准备的SQL文件
- **特点**：
  - 不包含 `CREATE DATABASE` 语句
  - 不包含 `USE` 语句
  - 格式清晰，便于phpMyAdmin解析
  - 包含所有20个表的创建语句
  - 包含初始数据插入语句

### schema.sql
- **用途**：完整的数据库结构文件
- **特点**：
  - 包含 `CREATE DATABASE` 和 `USE` 语句
  - 适合命令行导入
  - 也可以在phpMyAdmin中使用（会自动忽略CREATE DATABASE和USE）

## ⚠️ 常见问题

### 问题1：导入时提示"表已存在"
**解决**：
- SQL文件使用了 `CREATE TABLE IF NOT EXISTS`，不会报错
- 如果表已存在，会跳过创建，但会更新初始数据

### 问题2：外键约束错误
**解决**：
- 确保按顺序导入（schema文件已按正确顺序排列）
- 如果仍有问题，可以临时禁用外键检查：
  ```sql
  SET FOREIGN_KEY_CHECKS = 0;
  -- 导入SQL
  SET FOREIGN_KEY_CHECKS = 1;
  ```

### 问题3：字符集错误
**解决**：
- 确保数据库使用 `utf8mb4` 字符集
- 在phpMyAdmin导入时，选择字符集为 `utf8mb4`

### 问题4：文件太大无法上传
**解决**：
- 检查phpMyAdmin的 `upload_max_filesize` 设置
- 或者使用SQL命令方式导入

### 问题5：权限不足
**解决**：
- 确保数据库用户有 `CREATE TABLE` 权限
- 联系数据库管理员授予权限

## 🔍 验证SQL（可选）

导入后可以执行以下SQL验证：

```sql
-- 查看所有表
SHOW TABLES;

-- 应该返回20个表

-- 检查roles表数据
SELECT * FROM roles;
-- 应该看到4条记录

-- 检查users表数据
SELECT * FROM users;
-- 应该看到1条记录（admin）

-- 检查cash_closings表结构
DESC cash_closings;
-- 应该看到所有字段

-- 统计表数量
SELECT COUNT(*) as table_count 
FROM information_schema.tables 
WHERE table_schema = 'test_finance';
-- 应该返回20
```

## 📝 导入后的操作

1. **修改默认密码**（重要！）
   - 登录系统后，立即修改admin用户的密码
   - 或通过phpMyAdmin直接修改users表的password_hash字段

2. **配置数据库连接**
   - 确保 `app/config/db.php` 配置正确
   - 数据库名：`test_finance`
   - 用户名和密码正确

3. **测试功能**
   - 登录系统
   - 测试各个功能模块
   - 检查数据是否正确

## 🆘 需要帮助？

如果导入失败，请提供：
1. phpMyAdmin的错误信息
2. 数据库版本
3. 导入时的具体步骤

---

**文件位置**：`database/schema_for_phpmyadmin.sql`
**最后更新**：2024年12月

