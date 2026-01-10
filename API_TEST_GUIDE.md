# API测试指南

## 使用curl测试API

### 1. 登录获取Token

```bash
curl -X POST http://localhost/api.php?r=auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}' \
  -c cookies.txt
```

响应示例：
```json
{
  "success": true,
  "data": {
    "token": "session_id_here",
    "user": {
      "id": 1,
      "username": "admin",
      "display_name": "管理员",
      "role_key": "owner"
    }
  },
  "timestamp": "2024-12-20 10:30:00"
}
```

保存返回的token用于后续请求。

### 2. 获取当前用户信息

```bash
curl -X GET "http://localhost/api.php?r=auth/me" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -b cookies.txt
```

### 3. 获取交易列表

```bash
curl -X GET "http://localhost/api.php?r=transactions/list?page=1&limit=20" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -b cookies.txt
```

### 4. 创建交易

```bash
curl -X POST "http://localhost/api.php?r=transactions/create" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "income",
    "amount": 100000,
    "currency": "VND",
    "category_id": 1,
    "payment_method_id": 1,
    "occurred_at": "2024-12-20 10:00:00",
    "note": "测试交易"
  }' \
  -b cookies.txt
```

### 5. 获取员工列表

```bash
curl -X GET "http://localhost/api.php?r=employees/list?page=1&limit=20" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -b cookies.txt
```

### 6. 获取店长首页数据

```bash
curl -X GET "http://localhost/api.php?r=manager/dashboard" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -b cookies.txt
```

### 7. 创建巡店记录（带文件上传）

```bash
curl -X POST "http://localhost/api.php?r=inspections/create" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -F "store=coffee" \
  -F "floor=1F" \
  -F "spot_date=2024-12-20" \
  -F "status=ok" \
  -F "note=测试巡店" \
  -F "photos[]=@/path/to/photo1.jpg" \
  -F "photos[]=@/path/to/photo2.jpg" \
  -b cookies.txt
```

### 8. 获取周排表

```bash
curl -X GET "http://localhost/api.php?r=shifts/weeklySchedule" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -b cookies.txt
```

### 9. 保存周排表

```bash
curl -X POST "http://localhost/api.php?r=shifts/saveWeeklySchedule" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "week_start": "2024-12-16",
    "action": "save_draft",
    "schedule_data": {
      "1": {
        "2024-12-16": ["morning"],
        "2024-12-17": ["evening"]
      }
    }
  }' \
  -b cookies.txt
```

## 使用Postman测试

### 导入Collection

1. 打开Postman
2. 点击 Import
3. 创建新的Collection：`Teah Space API`
4. 添加环境变量：
   - `base_url`: `http://localhost/api.php`
   - `token`: (登录后设置)

### 请求示例

#### 登录请求
- **Method**: POST
- **URL**: `{{base_url}}?r=auth/login`
- **Headers**: `Content-Type: application/json`
- **Body**:
```json
{
  "username": "admin",
  "password": "admin123"
}
```
- **Tests** (保存token):
```javascript
if (pm.response.code === 200) {
    const jsonData = pm.response.json();
    if (jsonData.success && jsonData.data.token) {
        pm.environment.set("token", jsonData.data.token);
    }
}
```

#### 获取交易列表
- **Method**: GET
- **URL**: `{{base_url}}?r=transactions/list?page=1&limit=20`
- **Headers**: `Authorization: Bearer {{token}}`

## 使用JavaScript/Fetch测试

```javascript
// 登录
const loginResponse = await fetch('http://localhost/api.php?r=auth/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ username: 'admin', password: 'admin123' }),
  credentials: 'include'
});
const loginData = await loginResponse.json();
const token = loginData.data.token;

// 获取交易列表
const transactionsResponse = await fetch('http://localhost/api.php?r=transactions/list?page=1&limit=20', {
  headers: { 'Authorization': `Bearer ${token}` },
  credentials: 'include'
});
const transactionsData = await transactionsResponse.json();
console.log(transactionsData);
```

## 使用Python测试

```python
import requests

BASE_URL = 'http://localhost/api.php'

# 登录
login_response = requests.post(
    f'{BASE_URL}?r=auth/login',
    json={'username': 'admin', 'password': 'admin123'}
)
token = login_response.json()['data']['token']

# 获取交易列表
headers = {'Authorization': f'Bearer {token}'}
transactions_response = requests.get(
    f'{BASE_URL}?r=transactions/list?page=1&limit=20',
    headers=headers
)
print(transactions_response.json())
```

## 常见错误码

- `200` - 成功
- `400` - 请求错误（参数错误、验证失败）
- `401` - 未授权（需要登录）
- `403` - 禁止访问（权限不足）
- `404` - 资源不存在
- `500` - 服务器错误

## 测试检查清单

- [ ] 登录接口
- [ ] 获取用户信息
- [ ] 交易CRUD操作
- [ ] 员工CRUD操作
- [ ] 班次管理
- [ ] 周排功能
- [ ] 巡店创建（文件上传）
- [ ] 现金日结
- [ ] 任务管理
- [ ] 店长首页数据
- [ ] 报表统计
- [ ] CORS跨域请求
- [ ] 错误处理
- [ ] 权限验证

## 注意事项

1. **Token管理**: 登录后保存token，后续请求在Header中携带
2. **Session支持**: 使用`credentials: 'include'`或`-b cookies.txt`保持session
3. **文件上传**: 使用`multipart/form-data`格式，不要设置`Content-Type`头（浏览器会自动设置）
4. **CORS**: 前端开发服务器需要配置代理或确保后端CORS配置正确
