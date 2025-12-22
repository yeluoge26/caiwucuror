# 数据库清理指南

## 📋 概述

本指南用于清理测试数据，准备部署到生产环境。

## ⚠️ 重要警告

**执行清理脚本前，请务必备份数据库！**

### 备份命令

```bash
# 使用 mysqldump
mysqldump -u root -p coffee_finance > backup_$(date +%Y%m%d_%H%M%S).sql

# 或使用 phpMyAdmin 导出功能
```

## 📁 清理脚本说明

### 1. `cleanup_test_data.sql` - 快速清理脚本
- 直接删除所有测试数据
- 不包含确认步骤
- 适合确定要清理所有数据的情况

### 2. `cleanup_test_data_safe.sql` - 安全清理脚本（推荐）
- 包含数据统计查询（清理前后对比）
- 包含详细的注释和说明
- 包含员工删除选项（需要手动选择）
- 包含清理后验证

## 🗑️ 清理内容

### 将被删除的数据

1. **业务数据**
   - `transactions` - 所有交易记录
   - `inspections` - 所有巡店记录
   - `inspection_photos` - 所有巡店照片
   - `tasks` - 所有任务记录
   - `task_attachments` - 所有任务附件
   - `cash_closings` - 所有现金日结记录
   - `consumption_logs` - 所有消耗记录
   - `attachments` - 所有交易附件
   - `void_requests` - 所有作废申请
   - `shifts` - 所有班次记录
   - `assets` - 所有资产记录（如果存在）
   - `materials` - 所有材料记录（如果存在）

2. **测试员工数据**（可选）
   - 根据 `cleanup_test_data_safe.sql` 中的选项选择是否删除

### 将保留的数据

1. **基础系统数据**
   - `roles` - 角色表（系统必需）
   - `users` - 用户表（包括管理员账户）

2. **基础配置数据**
   - `categories` - 分类表
   - `payment_methods` - 支付方式表
   - `vendors` - 供应商表
   - `drinks` - 饮品表
   - `drink_recipes` - 配方表
   - `employees` - 员工表（如果未删除）

## 🚀 执行步骤

### 方式1：使用 MySQL 命令行

```bash
# 1. 备份数据库
mysqldump -u root -p coffee_finance > backup_$(date +%Y%m%d_%H%M%S).sql

# 2. 执行清理脚本（推荐使用安全版本）
mysql -u root -p coffee_finance < database/cleanup_test_data_safe.sql

# 3. 验证清理结果
mysql -u root -p coffee_finance -e "SELECT COUNT(*) FROM transactions;"
```

### 方式2：使用 phpMyAdmin

1. 登录 phpMyAdmin
2. 选择 `coffee_finance` 数据库
3. 点击"SQL"标签
4. 复制 `cleanup_test_data_safe.sql` 的内容
5. 粘贴到 SQL 编辑框
6. 点击"执行"

### 方式3：使用 MySQL Workbench

1. 打开 MySQL Workbench
2. 连接到数据库服务器
3. 打开 `cleanup_test_data_safe.sql` 文件
4. 执行脚本

## 📂 清理上传的文件

清理数据库后，还需要手动清理上传的文件：

```bash
# 进入项目目录
cd /www/wwwroot/test.TECHCOFFEEDANANG.COM/caiwucuror

# 删除所有上传的文件
rm -rf public/uploads/*

# 重新创建必要的目录结构
mkdir -p public/uploads/inspections
chmod 755 public/uploads public/uploads/inspections
```

## ✅ 清理后验证

执行清理脚本后，验证以下内容：

1. **数据库验证**
   ```sql
   SELECT COUNT(*) FROM transactions;      -- 应该为 0
   SELECT COUNT(*) FROM inspections;         -- 应该为 0
   SELECT COUNT(*) FROM tasks;               -- 应该为 0
   SELECT COUNT(*) FROM cash_closings;       -- 应该为 0
   ```

2. **基础数据验证**
   ```sql
   SELECT COUNT(*) FROM roles;               -- 应该 > 0（至少4个角色）
   SELECT COUNT(*) FROM users;               -- 应该 > 0（至少1个管理员）
   SELECT COUNT(*) FROM categories;           -- 应该 > 0
   SELECT COUNT(*) FROM payment_methods;      -- 应该 > 0
   ```

3. **文件系统验证**
   ```bash
   ls -la public/uploads/                    -- 应该为空或只有目录
   ```

## 🔄 重置管理员密码（可选）

如果需要重置管理员密码：

```sql
-- 重置 admin 用户密码为 admin123
UPDATE users 
SET password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE username = 'admin';
```

## 📝 注意事项

1. **备份优先**：执行清理前务必备份数据库
2. **员工数据**：根据实际情况决定是否删除员工数据
3. **文件清理**：数据库清理后记得清理上传的文件
4. **权限检查**：确保上传目录有正确的权限
5. **测试验证**：清理后测试系统基本功能是否正常

## 🆘 恢复数据

如果清理后需要恢复数据：

```bash
# 恢复备份
mysql -u root -p coffee_finance < backup_YYYYMMDD_HHMMSS.sql
```

## 📞 支持

如有问题，请检查：
1. 数据库连接是否正常
2. 用户权限是否足够
3. 外键约束是否阻止删除
4. 日志文件中的错误信息



