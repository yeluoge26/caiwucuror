# 班次状态字段迁移说明

## 概述

此迁移为 `shifts` 表添加 `status` 字段，用于记录员工在岗状态，支持以下状态：
- `pending` - 未确认（默认）
- `confirmed` - 已到岗
- `late` - 迟到
- `leave` - 请假
- `off` - 调休
- `abnormal` - 打卡异常

## 执行步骤

### 1. 执行数据库迁移

执行以下 SQL 文件：
```bash
database/add_shift_status.sql
```

或者在 phpMyAdmin 中直接执行以下 SQL：

```sql
-- 添加 status 字段
ALTER TABLE shifts 
ADD COLUMN status ENUM('pending', 'confirmed', 'late', 'leave', 'off', 'abnormal') 
NOT NULL DEFAULT 'pending' 
COMMENT '在岗状态：pending=未确认，confirmed=已到岗，late=迟到，leave=请假，off=调休，abnormal=打卡异常' 
AFTER is_confirmed;

-- 将现有的 is_confirmed 数据迁移到 status 字段
UPDATE shifts SET status = 'confirmed' WHERE is_confirmed = 1;
UPDATE shifts SET status = 'pending' WHERE is_confirmed = 0;

-- 添加索引以提高查询性能
ALTER TABLE shifts ADD INDEX idx_shifts_status (status);
```

### 2. 验证迁移

执行以下 SQL 验证迁移是否成功：

```sql
-- 检查字段是否存在
SHOW COLUMNS FROM shifts LIKE 'status';

-- 查看状态分布
SELECT status, COUNT(*) as count FROM shifts GROUP BY status;

-- 查看索引
SHOW INDEXES FROM shifts WHERE Key_name = 'idx_shifts_status';
```

## 功能说明

### 新增功能

1. **状态选择下拉框**
   - 在今日在岗员工页面，每个班次旁边显示状态选择下拉框
   - 支持选择：未确认、已到岗、迟到、请假、调休、打卡异常

2. **状态图标和颜色**
   - ⏳ 未确认（红色）
   - ✅ 已到岗（绿色）
   - ⏰ 迟到（橙色）
   - 📝 请假（蓝色）
   - 🏖️ 调休（紫色）
   - ⚠️ 打卡异常（深橙色）

3. **实时更新**
   - 选择状态后自动提交
   - 更新图标和状态文本
   - 刷新页面以更新统计信息

### API 变更

- `EmployeeController::confirmShift()` 方法现在接受 `status` 参数而不是 `confirmed` 参数
- `Shift::updateStatus()` 方法用于更新班次状态

## 向后兼容性

- `is_confirmed` 字段仍然保留，用于向后兼容
- 当状态为 `confirmed` 时，`is_confirmed` 自动设置为 1
- 其他状态时，`is_confirmed` 设置为 0

## 回滚（如果需要）

如果需要回滚此迁移：

```sql
-- 删除索引
ALTER TABLE shifts DROP INDEX idx_shifts_status;

-- 删除 status 字段
ALTER TABLE shifts DROP COLUMN status;
```

注意：回滚会丢失所有状态数据，请谨慎操作。






