-- 为shifts表添加周排相关字段
-- 用于支持周排功能：草稿保存、发布状态

-- 添加is_published字段（标记是否已发布）
-- 如果字段已存在，会报错但可以忽略
ALTER TABLE shifts 
ADD COLUMN is_published TINYINT(1) NOT NULL DEFAULT 0 
COMMENT '是否已发布：0=草稿，1=已发布' 
AFTER status;

-- 添加week_start_date字段（标记所属周的起始日期，周一）
ALTER TABLE shifts 
ADD COLUMN week_start_date DATE NULL 
COMMENT '所属周的起始日期（周一）' 
AFTER shift_date;

-- 添加索引以提高查询性能
ALTER TABLE shifts ADD INDEX idx_week_start_date (week_start_date);
ALTER TABLE shifts ADD INDEX idx_is_published (is_published);

-- 更新现有数据：将已有数据的week_start_date设置为对应周的周一
UPDATE shifts 
SET week_start_date = DATE_SUB(shift_date, INTERVAL WEEKDAY(shift_date) DAY)
WHERE week_start_date IS NULL;
