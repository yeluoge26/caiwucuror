1) 产品目标与范围

目标：让店里每一笔「收入 / 支出」都能被快速录入、可查、可统计、可导出，老板随时看利润与现金流。

范围（MVP）

录入收入（按订单/渠道/分类）

录入支出（按供应商/分类/付款方式）

日/周/月统计：总收入、总支出、毛利/净额、分类占比

账本流水列表 + 筛选 + 导出

角色权限（老板/店长/财务/员工）

中越双语 UI（ZH + VI）

不做：库存、POS 深度对接、工资复杂核算（可后续迭代）

2) 角色与权限（简单但够用）

Owner（老板）：全权限、查看所有统计、删除/作废记录

Manager（店长）：录入、查看、审批（可选）

Accountant（财务）：录入、审核、导出

Staff（员工）：只能新增记录，不能删除，只能看自己提交的（或看全部但不可改）

权限建议用 RBAC：roles + permissions + role_permissions。

3) 核心数据模型（数据库 MySQL）
3.1 账本主表：transactions（最核心）

id (PK)

type ENUM('income','expense') // 收入/支出

amount DECIMAL(12,2)

currency VARCHAR(8) default 'VND'

category_id (FK)

payment_method_id (FK) // 现金/转账/QR/VNPAY/ZaloPay…

occurred_at DATETIME // 发生时间

note TEXT

vendor_id (nullable) // 支出供应商

customer_tag (nullable) // 收入来源标记（外卖/堂食/团购）

created_by (FK users)

status ENUM('pending','approved','void') default 'approved'（MVP可直接approved）

created_at, updated_at

3.2 分类：categories

id

type ENUM('income','expense','both')

name_zh, name_vi

parent_id (nullable) // 二级分类可选

is_active

3.3 支付方式：payment_methods

id

name_zh, name_vi

is_active

3.4 供应商：vendors

id

name

phone

note

3.5 用户与角色

users：id, name, phone/email, password_hash, role_id, is_active

roles：id, key(owner/manager/accountant/staff), name_zh, name_vi

3.6 附件（可选但非常有用）：attachments

id, transaction_id, file_path, file_type, uploaded_by, created_at

MVP 最少：transactions + categories + payment_methods + users/roles。

4) 页面与H5信息架构（MVP）
登录

手机号/账号 + 密码

语言切换（ZH/VI）

首页 Dashboard

今日收入 / 今日支出 / 今日净额

本月累计

近7天曲线（H5图表：Chart.js）

记一笔（最快入口）

切换：收入 / 支出

金额

分类

支付方式

时间（默认当前）

备注

上传凭证（发票/收据照片）

流水列表

筛选：日期范围、类型、分类、支付方式、提交人

搜索：备注关键字/供应商

行点击进入详情（可作废/编辑权限控制）

统计报表

按天/周/月汇总

分类占比（饼图）

支付方式汇总

设置（管理可见）

分类管理（中越双语名称）

支付方式管理

用户管理

5) 多语言方案（ZH/VI）

建议：后端返回 i18n key，前端根据语言渲染
两种实现选一个：

方案A（简单快）：PHP 渲染 + 语言包数组

lang/zh.php、lang/vi.php

__('transaction.add') 这种 helper 取字符串

H5 页面由 PHP blade / twig / 原生模板输出

方案B（更现代）：H5 前端（Vue/React）+ JSON i18n

i18n/zh.json, i18n/vi.json

后端只负责 API

如果你想最省事：Laravel + Blade + Alpine.js 就够了（移动端也顺）。

6) PHP 技术架构建议（能跑得稳、好维护）
推荐栈：Laravel（强烈建议）

Auth、Migration、Validation、RBAC 都省很多时间

API + H5 同时支持很方便

目录建议：

/app/Http/Controllers（页面+API）

/app/Services（统计逻辑）

/resources/views（H5页面）

/resources/lang/zh & /resources/lang/vi

7) API 设计（即便你用 Blade，也建议按API写）

POST /api/login

GET /api/me

交易

POST /api/transactions 新增

GET /api/transactions 列表（支持 query：type/date_from/date_to/category/payment_method/created_by）

GET /api/transactions/{id}

PUT /api/transactions/{id}（权限控制）

POST /api/transactions/{id}/void 作废

基础数据

GET /api/categories?type=income|expense

GET /api/payment-methods

GET /api/vendors

统计

GET /api/reports/summary?range=today|7d|month

GET /api/reports/by-category?type=expense&range=month

GET /api/reports/by-payment?range=month

8) P0 开发拆分（你可以直接按 Sprint 开工）
Sprint 1（P0，先能用）

登录/权限

分类/支付方式（初始化数据）

新增收入/支出

流水列表 + 筛选

Dashboard 今日/本月汇总

Sprint 2（P0增强）

上传凭证

作废/审核（可选）

报表（分类饼图、日汇总）