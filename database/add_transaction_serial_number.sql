-- 为交易表添加流水号字段
-- 流水号格式：YYYYMMDD-EXP/INC-ID

ALTER TABLE transactions 
ADD COLUMN serial_number VARCHAR(32) NULL COMMENT '流水号：日期-类别-ID' AFTER id;

-- 为流水号添加索引以便查询
CREATE INDEX idx_tx_serial_number ON transactions(serial_number);

-- 为已有数据生成流水号
UPDATE transactions 
SET serial_number = CONCAT(
  DATE_FORMAT(created_at, '%Y%m%d'),
  '-',
  UPPER(SUBSTRING(`type`, 1, 3)),
  '-',
  LPAD(id, 6, '0')
)
WHERE serial_number IS NULL;

-- 将流水号字段设置为NOT NULL（在已有数据更新后）
ALTER TABLE transactions 
MODIFY COLUMN serial_number VARCHAR(32) NOT NULL COMMENT '流水号：日期-类别-ID';

