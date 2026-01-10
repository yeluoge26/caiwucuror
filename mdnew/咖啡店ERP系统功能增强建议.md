# 咖啡店ERP系统功能增强建议

**版本**: 1.0  
**日期**: 2026年1月10日  
**作者**: Manus AI  

---

## 目录

1. [现有功能概览](#1-现有功能概览)
2. [功能缺口分析](#2-功能缺口分析)
3. [建议新增功能](#3-建议新增功能)
4. [功能优先级矩阵](#4-功能优先级矩阵)
5. [开发路线图](#5-开发路线图)
6. [数据库扩展建议](#6-数据库扩展建议)

---

## 1. 现有功能概览

通过对您现有PHP系统代码的全面分析，系统目前包含以下功能模块：

### 1.1 现有功能模块清单

| 模块 | 功能 | 完成度 | 说明 |
|------|------|--------|------|
| **用户认证** | 登录/登出、角色权限（老板/店长/财务/员工） | 完整 | 4种角色，权限控制完善 |
| **财务管理** | 收入/支出记录、分类管理、支付方式、供应商管理 | 完整 | 支持审批、作废流程 |
| **现金日结** | 店长每日现金盘点、差额说明、审批流程 | 完整 | 理论金额自动计算 |
| **巡店检查** | 楼层/区域巡检、照片上传、审核流程 | 完整 | 支持首次/复巡 |
| **员工管理** | 员工档案、入职/离职管理 | 完整 | 全职/兼职区分 |
| **排班管理** | 班次创建、到岗确认 | 完整 | 早/中/晚三班 |
| **任务管理** | 固定/临时任务、指派、完成、审批 | 完整 | 支持奖惩金额 |
| **资产管理** | 固定资产登记、图片上传 | 完整 | 按店面/楼层分类 |
| **库存管理** | 原材料库存、安全库存预警 | 基础 | 缺少入库/出库记录 |
| **饮品配方** | 饮品与原料关联、消耗记录 | 基础 | 缺少成本核算 |
| **报表统计** | 今日/本月统计、7日趋势 | 基础 | 缺少高级分析 |
| **数据导入导出** | CSV导入导出 | 完整 | 支持交易数据 |

### 1.2 数据库结构统计

系统目前包含 **20个数据表**，涵盖用户、财务、资产、库存、巡店、任务、员工、班次等核心业务实体。

---

## 2. 功能缺口分析

对比行业标准咖啡店/餐饮ERP系统 [1] [2] [3]，您的系统存在以下功能缺口：

### 2.1 核心功能缺口

| 缺口类别 | 缺失功能 | 影响程度 | 行业普及率 |
|----------|----------|----------|------------|
| **销售管理** | POS收银、订单管理、菜单管理 | 高 | 95% |
| **客户管理** | 会员系统、积分/储值、消费记录 | 高 | 85% |
| **采购管理** | 采购订单、入库单、供应商对账 | 中 | 80% |
| **成本核算** | 饮品成本、毛利分析、损耗统计 | 中 | 75% |
| **营销功能** | 优惠券、促销活动、会员等级 | 中 | 70% |
| **多店管理** | 门店切换、数据汇总、对比分析 | 低 | 60% |
| **移动收银** | 手机/平板点单、扫码支付 | 中 | 65% |

### 2.2 现有功能增强需求

| 模块 | 增强需求 | 优先级 |
|------|----------|--------|
| **库存管理** | 入库单/出库单、批次管理、保质期预警、盘点功能 | 高 |
| **报表统计** | 商品销售排行、时段分析、同比环比、毛利报表 | 高 |
| **员工管理** | 考勤打卡、工时统计、薪资计算 | 中 |
| **巡店检查** | 检查模板、评分标准、整改跟踪 | 中 |
| **任务管理** | 周期性任务、任务模板、统计分析 | 低 |

---

## 3. 建议新增功能

### 3.1 销售与订单管理模块（高优先级）

作为咖啡店ERP的核心，销售管理模块是最重要的缺失功能 [1] [2]。

**3.1.1 菜单管理**

菜单管理是咖啡店运营的基础，需要支持灵活的商品配置和价格管理。

| 功能点 | 说明 |
|--------|------|
| 商品分类 | 支持多级分类（如：咖啡 > 美式/拿铁/卡布奇诺） |
| 商品信息 | 名称、图片、描述、价格、成本、SKU |
| 规格选项 | 杯型（小/中/大）、温度（冰/热）、糖度、加料 |
| 套餐组合 | 支持商品组合销售，自动计算优惠价 |
| 时段定价 | 早餐时段、下午茶时段差异化定价 |
| 商品状态 | 上架/下架/售罄管理 |

**3.1.2 订单管理**

订单管理需要支持从下单到完成的全流程跟踪。

| 功能点 | 说明 |
|--------|------|
| 订单创建 | 支持堂食、外带、外卖三种类型 |
| 订单状态 | 待支付 > 已支付 > 制作中 > 已完成 > 已取消 |
| 订单详情 | 商品明细、规格选项、备注、金额 |
| 支付方式 | 现金、刷卡、扫码支付（微信/支付宝/VNPay等） |
| 订单查询 | 按时间、状态、支付方式、操作员筛选 |
| 退款处理 | 部分退款、全额退款、退款原因记录 |

**3.1.3 收银台界面**

收银台是员工日常使用最频繁的界面，需要高效易用。

| 功能点 | 说明 |
|--------|------|
| 快速点单 | 商品网格布局，支持触屏操作 |
| 购物车 | 实时显示已选商品、数量、金额 |
| 快捷键 | 常用商品快捷按钮 |
| 挂单功能 | 支持多单并行处理 |
| 会员识别 | 扫码/手机号快速识别会员 |
| 小票打印 | 自动打印消费小票 |

### 3.2 客户关系管理模块（高优先级）

会员管理和客户关系维护是提升复购率的关键 [4] [5]。

**3.2.1 会员管理**

| 功能点 | 说明 |
|--------|------|
| 会员注册 | 手机号注册、微信授权、门店扫码注册 |
| 会员信息 | 姓名、手机、生日、偏好、标签 |
| 会员等级 | 普通/银卡/金卡/钻石，自动升降级 |
| 等级权益 | 折扣比例、积分倍数、专属活动 |
| 会员卡 | 实体卡/电子卡，支持卡号绑定 |

**3.2.2 积分系统**

| 功能点 | 说明 |
|--------|------|
| 积分获取 | 消费积分（1元=1分）、活动积分、签到积分 |
| 积分使用 | 积分抵现、积分兑换商品/优惠券 |
| 积分规则 | 可配置积分比例、有效期、使用门槛 |
| 积分记录 | 获取/使用明细、余额查询 |

**3.2.3 储值系统**

| 功能点 | 说明 |
|--------|------|
| 充值方案 | 充100送10、充500送80等 |
| 充值方式 | 现金、刷卡、在线支付 |
| 余额消费 | 支付时自动抵扣 |
| 余额查询 | 充值/消费记录、当前余额 |

**3.2.4 消费分析**

| 功能点 | 说明 |
|--------|------|
| 消费记录 | 会员历史订单、消费金额统计 |
| RFM分析 | 最近消费、消费频率、消费金额分析 |
| 偏好分析 | 常购商品、消费时段、支付偏好 |
| 流失预警 | 识别即将流失的会员，触发挽回活动 |

### 3.3 采购与库存增强模块（中优先级）

完善的采购和库存管理可以有效控制成本 [3]。

**3.3.1 采购管理**

| 功能点 | 说明 |
|--------|------|
| 采购申请 | 员工提交采购需求，店长审批 |
| 采购订单 | 选择供应商、商品、数量、预计到货日期 |
| 订单状态 | 待审批 > 已下单 > 已发货 > 已收货 > 已结算 |
| 收货验收 | 实际收货数量、质量检验、差异处理 |
| 供应商对账 | 应付款统计、付款记录、对账单 |

**3.3.2 库存增强**

| 功能点 | 说明 |
|--------|------|
| 入库单 | 采购入库、调拨入库、盘盈入库 |
| 出库单 | 销售出库、报损出库、调拨出库、盘亏出库 |
| 批次管理 | 按批次追踪库存，支持先进先出 |
| 保质期预警 | 临期商品提醒，过期商品自动下架 |
| 库存盘点 | 定期盘点、差异分析、盘点记录 |
| 库存预警 | 低于安全库存自动提醒 |

### 3.4 营销活动模块（中优先级）

营销功能可以有效促进销售和客户活跃 [4] [5]。

**3.4.1 优惠券管理**

| 功能点 | 说明 |
|--------|------|
| 券类型 | 满减券、折扣券、代金券、兑换券 |
| 发放方式 | 系统发放、活动领取、消费赠送、分享获取 |
| 使用规则 | 有效期、使用门槛、适用商品、叠加规则 |
| 核销记录 | 使用时间、订单关联、核销人员 |

**3.4.2 促销活动**

| 功能点 | 说明 |
|--------|------|
| 活动类型 | 限时折扣、满减活动、买赠活动、第二杯半价 |
| 活动时间 | 开始/结束时间、每日时段限制 |
| 活动商品 | 指定参与商品或分类 |
| 活动效果 | 参与人数、销售额、ROI分析 |

**3.4.3 会员日活动**

| 功能点 | 说明 |
|--------|------|
| 生日特权 | 生日当天/当月专属优惠 |
| 会员日 | 每周/每月固定会员日，积分翻倍 |
| 新客礼 | 首次消费专属优惠 |

### 3.5 高级报表与分析模块（中优先级）

数据分析能力是管理决策的重要支撑 [6]。

**3.5.1 销售分析**

| 报表 | 说明 |
|------|------|
| 销售日报/周报/月报 | 销售额、订单数、客单价趋势 |
| 商品销售排行 | TOP商品、滞销商品识别 |
| 时段分析 | 各时段销售分布，识别高峰/低谷 |
| 支付方式分析 | 各支付方式占比、趋势 |
| 同比环比分析 | 与去年同期、上月对比 |

**3.5.2 成本与毛利分析**

| 报表 | 说明 |
|------|------|
| 商品成本核算 | 基于配方计算单品成本 |
| 毛利分析 | 各商品/分类毛利率 |
| 损耗统计 | 原材料损耗、报废统计 |
| 成本趋势 | 原材料成本变化追踪 |

**3.5.3 客户分析**

| 报表 | 说明 |
|------|------|
| 新客/老客分析 | 新客占比、老客复购率 |
| 会员消费分析 | 会员贡献度、活跃度 |
| 客户价值分层 | 高价值/中价值/低价值客户分布 |
| 流失分析 | 流失客户特征、流失原因 |

**3.5.4 运营分析**

| 报表 | 说明 |
|------|------|
| 员工业绩 | 各员工销售额、订单数排名 |
| 坪效分析 | 单位面积产出（如适用） |
| 人效分析 | 人均产出、工时效率 |
| 库存周转 | 库存周转率、滞销预警 |

### 3.6 员工管理增强模块（低优先级）

**3.6.1 考勤管理**

| 功能点 | 说明 |
|--------|------|
| 打卡方式 | 手机GPS打卡、人脸识别、指纹打卡 |
| 打卡规则 | 上班/下班时间、迟到/早退判定 |
| 请假管理 | 请假申请、审批、假期余额 |
| 加班管理 | 加班申请、加班时长统计 |
| 考勤报表 | 月度考勤汇总、异常统计 |

**3.6.2 薪资管理**

| 功能点 | 说明 |
|--------|------|
| 薪资结构 | 基本工资、绩效工资、补贴、扣款 |
| 工时计算 | 基于考勤自动计算工时 |
| 绩效关联 | 销售提成、任务奖惩 |
| 工资条 | 自动生成、员工查看 |

### 3.7 通知与消息模块（低优先级）

**3.7.1 系统通知**

| 功能点 | 说明 |
|--------|------|
| 库存预警 | 低库存、临期商品通知 |
| 审批提醒 | 待审批任务、交易、日结提醒 |
| 异常告警 | 大额交易、异常操作告警 |
| 业绩播报 | 日销售额、目标完成度播报 |

**3.7.2 推送渠道**

| 渠道 | 说明 |
|------|------|
| 站内消息 | 系统内消息中心 |
| 短信通知 | 重要通知短信推送 |
| 微信推送 | 公众号/小程序消息推送 |
| 邮件通知 | 报表、对账单邮件发送 |

---

## 4. 功能优先级矩阵

根据业务价值和实现复杂度，建议按以下优先级开发：

### 4.1 优先级评估标准

| 维度 | 权重 | 说明 |
|------|------|------|
| 业务价值 | 40% | 对日常运营的重要程度 |
| 用户需求 | 30% | 用户反馈和期望程度 |
| 实现难度 | 20% | 开发工作量和技术复杂度 |
| 依赖关系 | 10% | 是否为其他功能的前置条件 |

### 4.2 功能优先级排序

| 优先级 | 功能模块 | 业务价值 | 实现难度 | 建议阶段 |
|--------|----------|----------|----------|----------|
| **P0** | 菜单管理 | 高 | 中 | 第一阶段 |
| **P0** | 订单管理 | 高 | 中 | 第一阶段 |
| **P0** | 收银台界面 | 高 | 高 | 第一阶段 |
| **P1** | 会员管理 | 高 | 中 | 第二阶段 |
| **P1** | 积分系统 | 中 | 中 | 第二阶段 |
| **P1** | 库存入库/出库 | 高 | 低 | 第二阶段 |
| **P2** | 储值系统 | 中 | 中 | 第三阶段 |
| **P2** | 优惠券管理 | 中 | 中 | 第三阶段 |
| **P2** | 采购管理 | 中 | 中 | 第三阶段 |
| **P2** | 高级报表 | 中 | 中 | 第三阶段 |
| **P3** | 促销活动 | 低 | 中 | 第四阶段 |
| **P3** | 考勤管理 | 低 | 中 | 第四阶段 |
| **P3** | 薪资管理 | 低 | 高 | 第四阶段 |
| **P3** | 多店管理 | 低 | 高 | 第四阶段 |

---

## 5. 开发路线图

### 5.1 第一阶段：销售核心（建议周期：6-8周）

**目标**：建立完整的销售闭环，实现基本的收银功能。

| 周次 | 任务 | 交付物 |
|------|------|--------|
| 1-2 | 菜单管理开发 | 商品CRUD、分类管理、规格选项 |
| 3-4 | 订单管理开发 | 订单创建、状态流转、订单查询 |
| 5-6 | 收银台界面开发 | 点单界面、购物车、支付处理 |
| 7-8 | 集成测试与优化 | 全流程测试、性能优化、Bug修复 |

### 5.2 第二阶段：客户与库存（建议周期：6-8周）

**目标**：建立会员体系，完善库存管理。

| 周次 | 任务 | 交付物 |
|------|------|--------|
| 1-2 | 会员管理开发 | 会员注册、信息管理、等级体系 |
| 3-4 | 积分系统开发 | 积分规则、获取/使用、记录查询 |
| 5-6 | 库存增强开发 | 入库单、出库单、批次管理 |
| 7-8 | 集成测试与优化 | 会员消费关联、库存销售联动 |

### 5.3 第三阶段：营销与分析（建议周期：6-8周）

**目标**：增加营销能力，提升数据分析能力。

| 周次 | 任务 | 交付物 |
|------|------|--------|
| 1-2 | 储值系统开发 | 充值方案、余额管理、消费抵扣 |
| 3-4 | 优惠券系统开发 | 券类型、发放、核销、统计 |
| 5-6 | 采购管理开发 | 采购订单、收货验收、供应商对账 |
| 7-8 | 高级报表开发 | 销售分析、成本分析、客户分析 |

### 5.4 第四阶段：运营增强（建议周期：4-6周）

**目标**：完善运营管理功能。

| 周次 | 任务 | 交付物 |
|------|------|--------|
| 1-2 | 促销活动开发 | 活动配置、效果统计 |
| 3-4 | 考勤管理开发 | 打卡、请假、考勤报表 |
| 5-6 | 通知系统开发 | 站内消息、推送通知 |

---

## 6. 数据库扩展建议

### 6.1 新增数据表设计

以下是建议新增的核心数据表：

**商品与菜单相关**

```sql
-- 商品表
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL COMMENT '分类ID',
  name VARCHAR(128) NOT NULL COMMENT '商品名称',
  name_vi VARCHAR(128) NULL COMMENT '越南语名称',
  image VARCHAR(255) NULL COMMENT '商品图片',
  description TEXT NULL COMMENT '描述',
  price DECIMAL(12,2) NOT NULL COMMENT '售价',
  cost DECIMAL(12,2) NULL COMMENT '成本价',
  sku VARCHAR(64) NULL COMMENT 'SKU编码',
  sort_order INT DEFAULT 0 COMMENT '排序',
  status ENUM('active','inactive','soldout') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 商品分类表
CREATE TABLE product_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  parent_id INT NULL COMMENT '父分类ID',
  name VARCHAR(64) NOT NULL,
  name_vi VARCHAR(64) NULL,
  icon VARCHAR(64) NULL,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1
);

-- 商品规格表
CREATE TABLE product_specs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  spec_type VARCHAR(32) NOT NULL COMMENT '规格类型：size/temperature/sugar/topping',
  spec_name VARCHAR(64) NOT NULL COMMENT '规格名称',
  price_adjust DECIMAL(12,2) DEFAULT 0 COMMENT '价格调整',
  is_default TINYINT(1) DEFAULT 0,
  sort_order INT DEFAULT 0
);
```

**订单相关**

```sql
-- 订单表
CREATE TABLE orders (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  order_no VARCHAR(32) NOT NULL UNIQUE COMMENT '订单号',
  order_type ENUM('dine_in','takeaway','delivery') NOT NULL COMMENT '订单类型',
  member_id INT NULL COMMENT '会员ID',
  table_no VARCHAR(16) NULL COMMENT '桌号',
  total_amount DECIMAL(12,2) NOT NULL COMMENT '订单总额',
  discount_amount DECIMAL(12,2) DEFAULT 0 COMMENT '优惠金额',
  pay_amount DECIMAL(12,2) NOT NULL COMMENT '实付金额',
  points_used INT DEFAULT 0 COMMENT '使用积分',
  points_earned INT DEFAULT 0 COMMENT '获得积分',
  payment_method_id INT NULL COMMENT '支付方式',
  payment_status ENUM('unpaid','paid','refunded','partial_refund') DEFAULT 'unpaid',
  order_status ENUM('pending','confirmed','making','completed','cancelled') DEFAULT 'pending',
  remark TEXT NULL COMMENT '订单备注',
  cashier_id INT NOT NULL COMMENT '收银员ID',
  paid_at DATETIME NULL,
  completed_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 订单明细表
CREATE TABLE order_items (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT NOT NULL,
  product_id INT NOT NULL,
  product_name VARCHAR(128) NOT NULL,
  specs JSON NULL COMMENT '规格选项JSON',
  quantity INT NOT NULL DEFAULT 1,
  unit_price DECIMAL(12,2) NOT NULL,
  total_price DECIMAL(12,2) NOT NULL,
  remark VARCHAR(255) NULL
);
```

**会员相关**

```sql
-- 会员表
CREATE TABLE members (
  id INT AUTO_INCREMENT PRIMARY KEY,
  member_no VARCHAR(32) NOT NULL UNIQUE COMMENT '会员号',
  phone VARCHAR(32) NOT NULL UNIQUE COMMENT '手机号',
  name VARCHAR(64) NULL COMMENT '姓名',
  gender ENUM('male','female','unknown') DEFAULT 'unknown',
  birthday DATE NULL,
  level_id INT DEFAULT 1 COMMENT '会员等级',
  points INT DEFAULT 0 COMMENT '当前积分',
  balance DECIMAL(12,2) DEFAULT 0 COMMENT '储值余额',
  total_consume DECIMAL(12,2) DEFAULT 0 COMMENT '累计消费',
  consume_count INT DEFAULT 0 COMMENT '消费次数',
  last_consume_at DATETIME NULL COMMENT '最后消费时间',
  status ENUM('active','frozen','cancelled') DEFAULT 'active',
  source VARCHAR(32) NULL COMMENT '来源渠道',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 会员等级表
CREATE TABLE member_levels (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(32) NOT NULL,
  min_points INT DEFAULT 0 COMMENT '升级所需积分',
  discount_rate DECIMAL(4,2) DEFAULT 1.00 COMMENT '折扣率',
  points_rate DECIMAL(4,2) DEFAULT 1.00 COMMENT '积分倍率',
  benefits TEXT NULL COMMENT '等级权益描述'
);

-- 积分记录表
CREATE TABLE points_logs (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  member_id INT NOT NULL,
  type ENUM('earn','use','expire','adjust') NOT NULL,
  points INT NOT NULL COMMENT '积分变动',
  balance INT NOT NULL COMMENT '变动后余额',
  source VARCHAR(32) NULL COMMENT '来源：order/activity/sign_in/adjust',
  reference_id BIGINT NULL COMMENT '关联ID',
  remark VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 储值记录表
CREATE TABLE balance_logs (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  member_id INT NOT NULL,
  type ENUM('recharge','consume','refund','adjust') NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  balance DECIMAL(12,2) NOT NULL COMMENT '变动后余额',
  gift_amount DECIMAL(12,2) DEFAULT 0 COMMENT '赠送金额',
  payment_method VARCHAR(32) NULL,
  reference_id BIGINT NULL,
  operator_id INT NULL,
  remark VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**库存增强**

```sql
-- 库存流水表
CREATE TABLE stock_logs (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  material_id INT NOT NULL,
  type ENUM('in','out') NOT NULL COMMENT '入库/出库',
  sub_type VARCHAR(32) NOT NULL COMMENT '细分类型：purchase/sale/loss/adjust/transfer',
  quantity DECIMAL(12,2) NOT NULL COMMENT '数量',
  before_qty DECIMAL(12,2) NOT NULL COMMENT '变动前库存',
  after_qty DECIMAL(12,2) NOT NULL COMMENT '变动后库存',
  unit_cost DECIMAL(12,2) NULL COMMENT '单位成本',
  batch_no VARCHAR(32) NULL COMMENT '批次号',
  expire_date DATE NULL COMMENT '保质期',
  reference_type VARCHAR(32) NULL COMMENT '关联单据类型',
  reference_id BIGINT NULL COMMENT '关联单据ID',
  operator_id INT NOT NULL,
  remark VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 采购订单表
CREATE TABLE purchase_orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_no VARCHAR(32) NOT NULL UNIQUE,
  vendor_id INT NOT NULL,
  status ENUM('draft','pending','approved','ordered','received','cancelled') DEFAULT 'draft',
  total_amount DECIMAL(12,2) DEFAULT 0,
  expected_date DATE NULL COMMENT '预计到货日期',
  received_date DATE NULL COMMENT '实际收货日期',
  remark TEXT NULL,
  created_by INT NOT NULL,
  approved_by INT NULL,
  approved_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 采购订单明细表
CREATE TABLE purchase_order_items (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  material_id INT NOT NULL,
  quantity DECIMAL(12,2) NOT NULL COMMENT '采购数量',
  unit_price DECIMAL(12,2) NOT NULL COMMENT '采购单价',
  received_qty DECIMAL(12,2) DEFAULT 0 COMMENT '已收货数量',
  remark VARCHAR(255) NULL
);
```

### 6.2 现有表结构调整建议

| 表名 | 调整建议 |
|------|----------|
| transactions | 增加 order_id 字段关联订单 |
| employees | 增加 user_id 字段关联系统用户 |
| materials | 增加 batch_tracking 字段启用批次管理 |
| consumption_logs | 增加 order_id 字段关联销售订单 |

---

## 参考资料

[1]: https://pos.toasttab.com/blog/coffee-shop-pos-system-1 "Coffee Shop POS: 5 Features That Make a Big Difference - Toast"
[2]: https://www.magestore.com/blog/features-of-coffee-shop-pos/ "Top 6 must-have features of coffee shop POS software - Magestore"
[3]: https://www.verbat.com/blog/erp-for-the-coffee-industry-a-comprehensive-guide/ "ERP for the Coffee Industry – A Comprehensive Guide - Verbat"
[4]: https://loyaltyplant.com/loyalty-program-for-coffee-shops/ "Loyalty Program for Coffee Shops - LoyaltyPlant"
[5]: https://www.openloyalty.io/insider/coffee-loyalty-programs-successful-examples "Coffee loyalty programs: 10 successful examples - Open Loyalty"
[6]: https://cloudkitchens.com/blog/restaurant-analytics/ "How restaurant analytics can transform your operations - CloudKitchens"

---

**文档结束**

如需进一步的技术咨询或开发支持，请随时联系。
