# 快速启动指南

## 前后端分离架构快速开始

### 1. 后端API（已就绪）

后端API已经可以直接使用，无需额外配置。

**测试API：**
```bash
# 登录测试
curl -X POST http://localhost/api.php?r=auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}'
```

### 2. 前端项目启动

#### 步骤1：进入前端目录
```bash
cd frontend
```

#### 步骤2：安装依赖
```bash
npm install
```

#### 步骤3：创建环境变量文件
创建 `frontend/.env` 文件：
```env
VITE_API_BASE_URL=http://localhost/api.php
VITE_APP_TITLE=Teah Space 管理系统
```

#### 步骤4：启动开发服务器
```bash
npm run dev
```

访问 http://localhost:5173

#### 步骤5：登录
- 用户名：`admin`
- 密码：`admin123`（根据实际数据库配置）

### 3. 测试API接口

#### 使用浏览器控制台测试

打开浏览器开发者工具（F12），在Console中运行：

```javascript
// 登录
const loginRes = await fetch('http://localhost/api.php?r=auth/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ username: 'admin', password: 'admin123' }),
  credentials: 'include'
});
const loginData = await loginRes.json();
console.log('Login:', loginData);

// 保存token
const token = loginData.data.token;
localStorage.setItem('token', token);

// 获取交易列表
const txRes = await fetch('http://localhost/api.php?r=transactions/list?page=1&limit=10', {
  headers: { 'Authorization': `Bearer ${token}` },
  credentials: 'include'
});
const txData = await txRes.json();
console.log('Transactions:', txData);
```

#### 使用Postman测试

1. 导入Postman Collection（参考 `API_TEST_GUIDE.md`）
2. 设置环境变量：
   - `base_url`: `http://localhost/api.php`
3. 先调用登录接口获取token
4. 使用token调用其他接口

### 4. 开发新功能

#### 添加新的API接口

1. 在 `app/controllers/api/` 目录创建新的API控制器
2. 在 `public/api.php` 中添加路由映射
3. 实现CRUD方法

#### 添加新的前端页面

1. 在 `frontend/src/pages/` 创建新页面组件
2. 在 `frontend/src/api/` 创建对应的API调用文件
3. 在 `frontend/src/components/Layout.tsx` 添加路由
4. 在菜单中添加导航项

### 5. 常见问题

#### CORS错误
- 确保后端 `app/core/Response.php` 中的CORS配置正确
- 检查前端开发服务器端口是否在允许列表中

#### 认证失败
- 检查token是否正确保存
- 确认请求Header中包含 `Authorization: Bearer {token}`
- 检查session是否过期

#### API 404错误
- 确认路由映射在 `public/api.php` 中已添加
- 检查API控制器文件是否存在
- 确认方法名是否正确

### 6. 生产部署

#### 后端部署
1. 上传PHP文件到服务器
2. 配置Web服务器（Apache/Nginx）
3. 设置数据库连接
4. 配置 `.htaccess`（如果需要）

#### 前端部署
1. 构建前端：`npm run build`
2. 将 `dist` 目录上传到Web服务器
3. 配置Nginx反向代理：
```nginx
location /api.php {
    proxy_pass http://localhost;
}
```

## 下一步

1. ✅ 后端API已完成 - 可以直接使用
2. ✅ 前端项目结构已创建 - 可以开始开发
3. 📝 参考文档：
   - `API_ARCHITECTURE.md` - API架构
   - `FRONTEND_SETUP.md` - 前端设置
   - `API_TEST_GUIDE.md` - API测试
   - `SEPARATION_COMPLETE.md` - 完成总结
