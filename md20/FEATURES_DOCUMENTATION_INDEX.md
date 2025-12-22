# 功能文档索引

> 本文档列出了所有新增功能的详细说明、代码位置和相关文档，方便后续功能扩展和维护。

---

## 📚 核心文档

### 1. PRD文档
- **文件**: `PRD-manager.md`
- **说明**: 店长管理系统增强版的产品需求文档
- **内容**: 
  - 背景与目标
  - 角色定义
  - 功能模块概览
  - 数据模型设计
  - 功能需求说明
  - UI约束与防作弊规则
  - 实现状态（2024年12月更新）
  - 开发任务清单
  - 技术实现细节
  - 数据库迁移脚本

### 2. 实现文档
- **文件**: `MANAGER_FEATURES_IMPLEMENTATION.md`
- **说明**: 功能实现总结和使用说明
- **内容**:
  - 已完成功能列表
  - 数据库迁移说明
  - 使用说明
  - 技术细节
  - 测试建议
  - 后续优化建议

---

## 🎯 新增功能模块

### 1. 现金日结功能（Cash Closing）

#### 📖 功能说明
店长每日现金对账功能，记录实盘现金并与理论现金余额对比，支持审核流程。

#### 📁 代码文件
```
app/models/CashClosing.php                    # 模型类
app/controllers/CashClosingController.php     # 控制器
app/views/cash_closings/create.php            # 创建/编辑视图
app/views/cash_closings/list.php              # 列表视图
app/views/cash_closings/view.php              # 详情/审核视图
database/add_cash_closings_table.sql          # 数据库迁移脚本
database/migrate_cash_closings.php            # PHP迁移脚本
public/migrate_cash_closings.php              # Web迁移脚本
```

#### 🔌 路由配置
- `cash_closings/create` - 创建现金日结
- `cash_closings/list` - 现金日结列表
- `cash_closings/view` - 查看详情
- `cash_closings/submit` - 提交审核
- `cash_closings/review` - 审核

#### 🗄️ 数据库表
- **表名**: `cash_closings`
- **迁移脚本**: `database/add_cash_closings_table.sql`
- **字段**:
  - `id` - 主键
  - `date` - 交账日期（唯一索引）
  - `created_by` - 店长ID
  - `cash_counted` - 实盘现金
  - `cash_note` - 差额说明
  - `status` - 状态（draft/submitted/approved/rejected）
  - `submitted_at` - 提交时间
  - `reviewed_by` - 审核人ID
  - `reviewed_at` - 审核时间
  - `review_note` - 审核备注

#### 🔑 核心方法
**CashClosing模型**:
- `find($id)` - 查找单条记录
- `findByDate($date)` - 按日期查找
- `create($data)` - 创建记录
- `update($id, $data)` - 更新记录
- `submit($id)` - 提交审核
- `review($id, $status, $note, $userId)` - 审核
- `calculateTheoreticalBalance($date, $openingCash)` - 计算理论现金余额
- `getPreviousClosing($date)` - 获取上一日日结记录

#### 📝 业务规则
1. 一天只能提交一次现金日结
2. 差额不为0时必须填写说明
3. 理论现金余额 = 期初现金 + 现金收入 - 现金支出
4. 期初现金从上一日的已审核日结记录中获取
5. 只有老板和财务可以审核

#### 🌐 国际化
- 中文: `lang/zh.php` - `cash_closing.*`
- 越南语: `lang/vi.php` - `cash_closing.*`

---

### 2. 店长首页（Manager Dashboard）

#### 📖 功能说明
店长专用的管理态首页，显示今日管理清单和关键指标。

#### 📁 代码文件
```
app/controllers/ManagerController.php         # 控制器
app/views/manager/dashboard.php               # 首页视图
```

#### 🔌 路由配置
- `manager/dashboard` - 店长首页
- **默认路由**: 店长角色登录后自动跳转

#### 🎨 页面组件
1. **今日信息卡片** - 显示当前日期
2. **今日任务卡片** - 显示待处理任务数量
3. **今日巡店卡片** - 显示已完成次数（🔴🟡🟢）
4. **今日现金日结卡片** - 显示状态（🔴🟡🟠🟢）
5. **本周排班卡片** - 显示排班预览
6. **本周排班预览表格** - 员工 × 周一到周日
7. **今日任务列表** - 显示前5个任务

#### 🔑 核心方法
**ManagerController**:
- `dashboard()` - 首页数据加载
  - 统计今日巡店次数
  - 获取今日现金日结状态
  - 获取今日任务列表
  - 获取本周排班数据

#### 📊 状态计算逻辑

**巡店状态**:
- 🔴: 0次有效巡店
- 🟡: 1次有效巡店
- 🟢: ≥2次有效巡店

**现金日结状态**:
- 🔴: 未交账
- 🟡: 已交账未审 / 草稿
- 🟠: 差额无说明
- 🟢: 已审核

#### 🌐 国际化
- 中文: `lang/zh.php` - `manager.*`
- 越南语: `lang/vi.php` - `manager.*`

---

### 3. 巡店功能增强

#### 📖 功能说明
增强现有巡店功能，添加照片必填验证和禁止回填历史日期。

#### 📁 代码文件
```
app/controllers/InspectionsController.php      # 控制器（已修改）
app/views/inspections/list_form.php          # 表单视图（已修改）
```

#### 🔧 增强内容
1. **照片必填验证**
   - 创建巡店时必须上传至少1张照片
   - 前端和后端双重验证

2. **禁止回填历史日期**
   - `spot_date` 不能是过去日期
   - 只能选择今天或未来的日期

3. **表单提示更新**
   - 添加照片必填提示
   - 添加日期限制提示

#### 🔑 修改的方法
**InspectionsController::create()**:
- 添加日期验证逻辑
- 添加照片必填验证
- 错误提示优化

#### 🌐 国际化
- 中文: `lang/zh.php` - `inspection.no_past_date`, `inspection.photo_required`
- 越南语: `lang/vi.php` - `inspection.no_past_date`, `inspection.photo_required`

---

## 🔧 系统增强

### 1. 路由配置更新

#### 📁 文件
```
app/core/Router.php
```

#### 🔧 修改内容
- 添加 `cash_closings` 路由映射
- 添加 `manager` 路由映射
- 店长角色默认跳转到店长首页

#### 📝 代码位置
```php
$controllerMap = [
  'cash_closings' => 'CashClosing',
  'manager' => 'Manager'
];
```

---

### 2. 导航菜单更新

#### 📁 文件
```
app/views/layout/header.php
```

#### 🔧 修改内容
- 店长角色显示专用导航菜单
- 添加现金日结链接
- 店长首页作为默认首页

---

### 3. 模型增强

#### PaymentMethod模型
**文件**: `app/models/PaymentMethod.php`
**新增方法**:
- `findByName($nameZh, $nameVi)` - 按名称查找支付方式

#### Transaction模型
**文件**: `app/models/Transaction.php`
**增强方法**:
- `getSummary($filters)` - 支持 `payment_method_id` 和 `type` 过滤器

---

## 📋 待实现功能（P1优先级）

### 1. 排班周排功能增强

#### 📖 功能说明
增强排班功能，支持周排表格视图、一键复制上周、草稿保存等。

#### 📝 需要实现的功能
- [ ] 周排页面（员工 × 周一到周日表格视图）
- [ ] 单元格点击循环：休 → 早 → 晚 → 休
- [ ] 一键复制上周功能
- [ ] 草稿保存功能
- [ ] 发布本周排班功能

#### 📁 相关文件
```
app/controllers/ShiftController.php          # 需要添加weeklySchedule方法
app/views/shifts/weekly_schedule.php         # 需要创建
app/models/Shift.php                         # 可能需要增强
```

#### 📚 参考文档
- `PRD-manager.md` - 第5.4节：排班（周排 + 调整）
- `员工与班次管理功能说明.md` - 现有排班功能说明

---

## 🗄️ 数据库相关

### 迁移脚本
- `database/add_cash_closings_table.sql` - 现金日结表创建脚本
- `database/migrate_cash_closings.php` - PHP命令行迁移脚本
- `public/migrate_cash_closings.php` - Web界面迁移脚本

### 执行迁移
```bash
# 方式1: Web界面（推荐）
访问: http://localhost/migrate_cash_closings.php

# 方式2: 命令行
php database/migrate_cash_closings.php

# 方式3: MySQL直接执行
mysql -u root -p coffee_finance < database/add_cash_closings_table.sql
```

---

## 🌐 国际化翻译

### 新增翻译键

#### 中文 (`lang/zh.php`)
- `cash_closing.*` - 现金日结相关翻译（约30个键）
- `manager.*` - 店长首页相关翻译（约15个键）
- `inspection.no_past_date` - 禁止回填历史日期
- `inspection.photo_required` - 照片必填
- `shift.morning/afternoon/evening` - 班次类型

#### 越南语 (`lang/vi.php`)
- 对应的越南语翻译

---

## 🧪 测试相关

### 测试脚本
- `test_manager_features.php` - 功能测试脚本
  - 检查数据库连接
  - 检查表结构
  - 检查文件是否存在
  - 检查路由配置
  - 检查国际化翻译

### 测试建议
1. **现金日结功能测试**
   - 创建现金日结
   - 测试差额验证
   - 测试提交和审核流程

2. **店长首页测试**
   - 验证各卡片状态显示
   - 验证点击跳转功能
   - 验证数据统计准确性

3. **巡店功能测试**
   - 测试照片必填验证
   - 测试禁止回填历史日期
   - 测试每日2次巡店统计

---

## 📖 其他相关文档

### 功能说明文档
- `任务管理功能说明.md` - 任务系统功能说明
- `员工与班次管理功能说明.md` - 员工和班次管理说明
- `交易列表分页和导入导出功能说明.md` - 交易功能说明

### 系统文档
- `完整功能清单.md` - 系统完整功能列表
- `功能列表分析.md` - 功能分析报告
- `CODE_ANALYSIS.md` - 代码分析报告

### 开发文档
- `ARCHITECTURE.md` - 系统架构说明
- `INSTALLATION.md` - 安装说明
- `TEST_GUIDE.md` - 测试指南

---

## 🚀 快速开始

### 1. 执行数据库迁移
```bash
访问: http://localhost/migrate_cash_closings.php
或执行: php database/migrate_cash_closings.php
```

### 2. 测试功能
```bash
访问: http://localhost/test_manager_features.php
```

### 3. 使用新功能
- 店长角色登录 → 自动跳转到店长首页
- 创建现金日结 → `cash_closings/create`
- 查看现金日结列表 → `cash_closings/list`

---

## 📝 开发规范

### 代码结构
```
app/
├── controllers/          # 控制器
│   ├── CashClosingController.php
│   └── ManagerController.php
├── models/              # 模型
│   └── CashClosing.php
└── views/               # 视图
    ├── cash_closings/
    └── manager/
```

### 命名规范
- 控制器: `XxxController.php`
- 模型: `Xxx.php`
- 视图: `xxx/action.php`
- 路由: `xxx/action`

### 权限控制
- 使用 `Auth::requireRole(['role1', 'role2'])` 进行权限验证
- 现金日结创建: 仅店长
- 现金日结审核: 老板、财务
- 店长首页: 仅店长

---

## 🔗 相关链接

- GitHub仓库: https://github.com/yeluoge26/caiwucuror
- PRD文档: `PRD-manager.md`
- 实现文档: `MANAGER_FEATURES_IMPLEMENTATION.md`
- Git推送说明: `GIT_PUSH_INSTRUCTIONS.md`
- Token问题排查: `TOKEN_TROUBLESHOOTING.md`

---

## 📅 更新日志

### 2024年12月
- ✅ 实现现金日结功能
- ✅ 实现店长首页
- ✅ 增强巡店功能
- ✅ 更新路由和导航
- ✅ 添加国际化翻译
- ✅ 创建功能文档

---

## 💡 后续扩展建议

### 功能扩展
1. **排班周排功能** - 实现周排表格和复制功能
2. **数据统计** - 添加巡店完成率、现金日结完成率统计
3. **移动端优化** - 优化移动端显示和操作
4. **实时通知** - 添加待办事项通知
5. **数据导出** - 支持导出现金日结报表

### 技术优化
1. **API接口** - 为移动端提供RESTful API
2. **缓存优化** - 添加数据缓存提升性能
3. **日志系统** - 添加操作日志记录
4. **单元测试** - 添加自动化测试

---

**最后更新**: 2024年12月
**维护者**: 开发团队

