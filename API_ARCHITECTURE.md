# API架构设计文档

## 概述

本项目采用前后端分离架构，后端提供RESTful API接口，前端使用现代框架（React/Vue）进行开发。

## API基础信息

- **基础URL**: `/api.php`
- **认证方式**: Session-based (支持Bearer Token)
- **数据格式**: JSON
- **字符编码**: UTF-8

## 统一响应格式

### 成功响应
```json
{
  "success": true,
  "data": {...},
  "message": "操作成功",
  "timestamp": "2024-12-20 10:30:00"
}
```

### 错误响应
```json
{
  "success": false,
  "error": "错误消息",
  "errors": {...},  // 可选，字段验证错误
  "timestamp": "2024-12-20 10:30:00"
}
```

## API端点列表

### 认证相关 (`/api.php?r=auth/`)
- `POST /api.php?r=auth/login` - 登录
- `GET /api.php?r=auth/me` - 获取当前用户信息
- `POST /api.php?r=auth/logout` - 登出

### 交易管理 (`/api.php?r=transactions/`)
- `GET /api.php?r=transactions/list` - 获取交易列表
- `GET /api.php?r=transactions/view&id={id}` - 获取交易详情
- `POST /api.php?r=transactions/create` - 创建交易
- `PUT /api.php?r=transactions/update&id={id}` - 更新交易
- `DELETE /api.php?r=transactions/void&id={id}` - 作废交易

### 员工管理 (`/api.php?r=employees/`)
- `GET /api.php?r=employees/list` - 获取员工列表
- `GET /api.php?r=employees/view&id={id}` - 获取员工详情
- `POST /api.php?r=employees/create` - 创建员工
- `PUT /api.php?r=employees/update&id={id}` - 更新员工
- `DELETE /api.php?r=employees/delete&id={id}` - 删除员工
- `GET /api.php?r=employees/today` - 获取今日在岗员工

### 班次管理 (`/api.php?r=shifts/`)
- `GET /api.php?r=shifts/list` - 获取班次列表
- `GET /api.php?r=shifts/view&id={id}` - 获取班次详情
- `POST /api.php?r=shifts/create` - 创建班次
- `PUT /api.php?r=shifts/update&id={id}` - 更新班次
- `POST /api.php?r=shifts/confirmShift` - 确认到岗状态
- `GET /api.php?r=shifts/weeklySchedule` - 获取周排表
- `POST /api.php?r=shifts/saveWeeklySchedule` - 保存周排表

### 巡店管理 (`/api.php?r=inspections/`)
- `GET /api.php?r=inspections/list` - 获取巡店列表
- `GET /api.php?r=inspections/view&id={id}` - 获取巡店详情
- `POST /api.php?r=inspections/create` - 创建巡店记录
- `POST /api.php?r=inspections/review&id={id}` - 审核巡店
- `POST /api.php?r=inspections/batchApprove` - 批量审核

### 现金日结 (`/api.php?r=cash_closings/`)
- `GET /api.php?r=cash_closings/list` - 获取日结列表
- `GET /api.php?r=cash_closings/view&id={id}` - 获取日结详情
- `POST /api.php?r=cash_closings/create` - 创建日结
- `POST /api.php?r=cash_closings/submit&id={id}` - 提交日结
- `POST /api.php?r=cash_closings/review&id={id}` - 审核日结

### 任务管理 (`/api.php?r=tasks/`)
- `GET /api.php?r=tasks/list` - 获取任务列表
- `GET /api.php?r=tasks/view&id={id}` - 获取任务详情
- `POST /api.php?r=tasks/create` - 创建任务
- `PUT /api.php?r=tasks/update&id={id}` - 更新任务
- `POST /api.php?r=tasks/approve&id={id}` - 审批任务

### 店长首页 (`/api.php?r=manager/`)
- `GET /api.php?r=manager/dashboard` - 获取店长首页数据

### 报表统计 (`/api.php?r=reports/`)
- `GET /api.php?r=reports/dashboard` - 获取仪表盘数据
- `GET /api.php?r=reports/summary` - 获取汇总统计
- `GET /api.php?r=reports/trends` - 获取趋势数据

### 基础数据 (`/api.php?r=categories/`, `/api.php?r=payment_methods/`, `/api.php?r=vendors/`)
- `GET /api.php?r=categories/list` - 获取分类列表
- `GET /api.php?r=payment_methods/list` - 获取支付方式列表
- `GET /api.php?r=vendors/list` - 获取供应商列表

## CORS配置

API支持跨域请求，允许的前端地址：
- `http://localhost:3000` (React默认端口)
- `http://localhost:5173` (Vite默认端口)
- `http://localhost:8080` (Vue CLI默认端口)

## 认证机制

### Session认证
使用PHP Session进行认证，前端需要：
1. 登录后保存session_id作为token
2. 后续请求在Header中携带：`Authorization: Bearer {session_id}`

### 示例
```javascript
// 登录
const response = await fetch('/api.php?r=auth/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ username, password })
});
const { data } = await response.json();
const token = data.token; // session_id

// 后续请求
fetch('/api.php?r=transactions/list', {
  headers: {
    'Authorization': `Bearer ${token}`
  }
});
```

## 错误码

- `200` - 成功
- `400` - 请求错误（参数错误、验证失败等）
- `401` - 未授权（需要登录）
- `403` - 禁止访问（权限不足）
- `404` - 资源不存在
- `500` - 服务器错误

## 文件上传

文件上传使用multipart/form-data格式：
```javascript
const formData = new FormData();
formData.append('file', file);
formData.append('_csrf', csrfToken);

fetch('/api.php?r=inspections/create', {
  method: 'POST',
  body: formData
});
```

## 分页

列表接口支持分页参数：
- `page` - 页码（从1开始）
- `limit` - 每页数量（默认20）

响应中包含分页信息：
```json
{
  "success": true,
  "data": [...],
  "pagination": {
    "page": 1,
    "limit": 20,
    "total": 100,
    "totalPages": 5
  }
}
```
