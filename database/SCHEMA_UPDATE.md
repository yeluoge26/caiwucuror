# 数据库Schema更新说明

## 📋 更新日期
2024年12月

## ✅ 更新内容

### 完整的数据库结构
`database/schema.sql` 已重写，包含所有最新表结构，整合了所有迁移脚本。

### 包含的数据表（共20个）

#### 1. 基础系统表（2个）
- `roles` - 角色表
- `users` - 用户表

#### 2. 财务交易相关表（6个）
- `categories` - 分类表
- `payment_methods` - 支付方式表
- `vendors` - 供应商表
- `transactions` - 交易表（核心）
- `attachments` - 交易附件表
- `void_requests` - 作废申请表

#### 3. 现金日结表（1个）✨ 新增
- `cash_closings` - 现金日结表

#### 4. 资产管理相关表（2个）
- `assets` - 固定资产表
- `asset_attachments` - 固定资产图片表

#### 5. 材料库存相关表（2个）
- `materials` - 原料库存表
- `material_attachments` - 原料附件表

#### 6. 饮品管理相关表（3个）
- `drinks` - 饮品表
- `drink_recipes` - 饮品配方表
- `consumption_logs` - 耗材消耗记录表

#### 7. 巡店检查相关表（2个）
- `inspections` - 巡店记录表
- `inspection_photos` - 巡店照片表

#### 8. 任务管理相关表（2个）
- `tasks` - 任务表
- `task_attachments` - 任务附件表

#### 9. 员工和班次管理相关表（2个）
- `employees` - 员工表（包含employment_type字段）
- `shifts` - 班次表（manager_id可为空）

## 🚀 使用方法

### 方式1：全新安装（推荐）

如果数据库是全新的，直接执行schema.sql：

```bash
# MySQL命令行
mysql -u root -p < database/schema.sql

# 或使用MySQL客户端
mysql -u root -p
source database/schema.sql;
```

### 方式2：已有数据库更新

如果数据库已存在，可以使用以下方式：

#### 选项A：使用迁移脚本（推荐）
```bash
# 只执行新增表的迁移脚本
mysql -u root -p coffee_finance < database/add_cash_closings_table.sql
```

#### 选项B：手动执行SQL
```sql
USE coffee_finance;

-- 检查表是否存在
SHOW TABLES LIKE 'cash_closings';

-- 如果不存在，执行创建语句
-- （从schema.sql中复制cash_closings表的CREATE TABLE语句）
```

### 方式3：Web界面迁移

访问迁移脚本：
```
http://localhost/migrate_cash_closings.php
```

## 📊 表结构对比

### 新增的表
- ✅ `cash_closings` - 现金日结表

### 更新的表
- ✅ `employees` - 添加了 `employment_type` 字段
- ✅ `shifts` - `manager_id` 字段可为空（支持取消负责人）

### 保持不变的表
- 所有其他表结构保持不变，兼容现有数据

## 🔍 验证数据库结构

执行以下SQL验证所有表是否创建成功：

```sql
USE coffee_finance;

-- 查看所有表
SHOW TABLES;

-- 验证cash_closings表结构
DESC cash_closings;

-- 验证employees表结构（检查employment_type字段）
DESC employees;

-- 验证shifts表结构（检查manager_id字段）
DESC shifts;
```

## 📝 重要说明

1. **备份数据**：在执行任何数据库操作前，请先备份现有数据库
   ```bash
   mysqldump -u root -p coffee_finance > backup_$(date +%Y%m%d).sql
   ```

2. **数据安全**：schema.sql使用 `CREATE TABLE IF NOT EXISTS`，不会删除现有数据

3. **外键约束**：所有外键关系已正确定义，确保数据完整性

4. **索引优化**：所有必要的索引已创建，提升查询性能

5. **字符集**：统一使用 `utf8mb4` 字符集，支持emoji和特殊字符

## 🔄 从旧版本升级

### 如果使用旧版本的schema.sql

1. **备份现有数据库**
   ```bash
   mysqldump -u root -p coffee_finance > backup.sql
   ```

2. **检查缺失的表**
   ```sql
   -- 检查cash_closings表是否存在
   SHOW TABLES LIKE 'cash_closings';
   ```

3. **执行缺失表的创建**
   - 如果cash_closings不存在，执行 `database/add_cash_closings_table.sql`
   - 如果employees表缺少employment_type字段，执行 `database/add_employee_employment_type.sql`

4. **验证完整性**
   ```sql
   -- 应该看到20个表
   SELECT COUNT(*) as table_count FROM information_schema.tables 
   WHERE table_schema = 'coffee_finance';
   ```

## 📚 相关文件

- `database/schema.sql` - 完整的数据库结构（最新版）
- `database/add_cash_closings_table.sql` - 现金日结表迁移脚本
- `database/add_employee_employment_type.sql` - 员工表字段更新脚本
- `database/migrate_cash_closings.php` - PHP迁移脚本
- `public/migrate_cash_closings.php` - Web迁移脚本

## ⚠️ 注意事项

1. **不要重复执行**：如果表已存在，重复执行schema.sql不会报错，但也不会更新表结构

2. **字段更新**：如果表已存在但缺少某些字段，需要使用ALTER TABLE语句

3. **数据迁移**：schema.sql只创建表结构，不迁移数据

4. **权限要求**：执行schema.sql需要CREATE TABLE权限

## 🆘 问题排查

### 问题1：表已存在错误
**解决**：schema.sql使用 `IF NOT EXISTS`，不会报错

### 问题2：外键约束错误
**解决**：确保先创建被引用的表（如roles表必须在users表之前创建）

### 问题3：字符集问题
**解决**：确保数据库使用utf8mb4字符集
```sql
ALTER DATABASE coffee_finance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 问题4：权限不足
**解决**：确保数据库用户有CREATE TABLE权限
```sql
GRANT CREATE ON coffee_finance.* TO 'your_user'@'localhost';
FLUSH PRIVILEGES;
```

## 📞 需要帮助？

如果遇到问题，请检查：
1. MySQL版本（建议5.7+或8.0+）
2. 数据库用户权限
3. 字符集设置
4. 外键约束

---

**最后更新**：2024年12月
**维护者**：开发团队



