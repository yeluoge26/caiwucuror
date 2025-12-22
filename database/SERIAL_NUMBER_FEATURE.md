# 财务流水号功能说明

## 功能概述

为财务流水（交易记录）添加了流水号功能，方便查询和管理。

## 流水号格式

流水号格式：`YYYYMMDD-类别-ID`

- **YYYYMMDD**: 8位日期，基于记录的创建时间（created_at）
- **类别**: 
  - `EXP` - 支出（expense）
  - `INC` - 收入（income）
- **ID**: 6位数字，左补零的交易ID

### 示例

- `20240115-EXP-000123` - 2024年1月15日创建的支出交易，ID为123
- `20240115-INC-000456` - 2024年1月15日创建的收入交易，ID为456

## 数据库更新

### 1. 添加字段

执行以下SQL脚本添加流水号字段：

```sql
-- 文件：database/add_transaction_serial_number.sql
ALTER TABLE transactions 
ADD COLUMN serial_number VARCHAR(32) NULL COMMENT '流水号：日期-类别-ID' AFTER id;

CREATE INDEX idx_tx_serial_number ON transactions(serial_number);
```

### 2. 为已有数据生成流水号

执行SQL脚本自动为已有数据生成流水号：

```sql
-- 在 add_transaction_serial_number.sql 中已包含
UPDATE transactions 
SET serial_number = CONCAT(
  DATE_FORMAT(created_at, '%Y%m%d'),
  '-',
  UPPER(SUBSTRING(`type`, 1, 3)),
  '-',
  LPAD(id, 6, '0')
)
WHERE serial_number IS NULL;
```

或者使用PHP脚本：

```bash
php database/generate_serial_numbers.php
```

### 3. 设置字段为必填

在已有数据更新完成后：

```sql
ALTER TABLE transactions 
MODIFY COLUMN serial_number VARCHAR(32) NOT NULL COMMENT '流水号：日期-类别-ID';
```

## 功能特性

### 1. 自动生成

- 新创建的交易记录会自动生成流水号
- 流水号基于记录的创建时间（created_at）和交易类型

### 2. 显示位置

- **交易列表页**: 流水号显示在第一列
- **交易详情页**: 流水号显示在顶部，使用等宽字体突出显示
- **导出功能**: Excel和CSV导出都包含流水号列

### 3. 查询支持

- 流水号字段已建立索引，支持快速查询
- 可以通过SQL查询按流水号搜索：
  ```sql
  SELECT * FROM transactions WHERE serial_number LIKE '20240115-%';
  ```

## 使用说明

### 查看流水号

1. 进入交易列表页面
2. 流水号显示在表格的第一列
3. 点击交易记录查看详情，流水号显示在详情页顶部

### 导出包含流水号

1. 在交易列表页面点击"导出"按钮
2. 导出的Excel或CSV文件会包含流水号列

## 技术实现

### 代码修改

1. **数据库模型** (`app/models/Transaction.php`)
   - `create()` 方法：创建记录后自动生成流水号

2. **视图文件**
   - `app/views/transactions/list.php`: 添加流水号列
   - `app/views/transactions/view.php`: 显示流水号

3. **导出功能**
   - `app/controllers/ExportController.php`: Excel导出包含流水号
   - `app/controllers/TransactionController.php`: CSV导出包含流水号

4. **语言包**
   - `lang/zh.php`: 添加中文翻译 "流水号"
   - `lang/vi.php`: 添加越南语翻译 "Số phiếu"

## 注意事项

1. **日期格式**: 流水号使用记录的创建时间（created_at），不是发生时间（occurred_at）
2. **唯一性**: 流水号包含交易ID，保证唯一性
3. **格式固定**: 流水号格式固定为 `YYYYMMDD-类别-ID`，共20-21个字符
4. **向后兼容**: 已有数据会通过迁移脚本自动生成流水号

## 更新日期

2024年

