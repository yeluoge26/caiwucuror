# 前端项目设置指南

## 概述

本项目采用前后端分离架构，前端可以使用任何现代框架（React、Vue、Angular等）。本文档提供前端项目的基础设置指南。

## 推荐技术栈

### React + TypeScript + Vite
- **React 18+** - UI框架
- **TypeScript** - 类型安全
- **Vite** - 构建工具
- **React Router** - 路由
- **Axios** - HTTP客户端
- **Zustand/Redux** - 状态管理
- **Ant Design / Material-UI** - UI组件库

### Vue 3 + TypeScript + Vite
- **Vue 3** - UI框架
- **TypeScript** - 类型安全
- **Vite** - 构建工具
- **Vue Router** - 路由
- **Axios** - HTTP客户端
- **Pinia** - 状态管理
- **Element Plus / Ant Design Vue** - UI组件库

## 快速开始（React + Vite）

### 1. 创建项目

```bash
npm create vite@latest frontend -- --template react-ts
cd frontend
npm install
```

### 2. 安装依赖

```bash
npm install axios react-router-dom zustand
npm install antd @ant-design/icons
npm install dayjs
```

### 3. 项目结构

```
frontend/
├── src/
│   ├── api/              # API调用
│   │   ├── client.ts     # Axios实例配置
│   │   ├── auth.ts       # 认证相关API
│   │   ├── transactions.ts
│   │   ├── employees.ts
│   │   └── ...
│   ├── store/            # 状态管理
│   │   ├── authStore.ts
│   │   └── ...
│   ├── components/       # 公共组件
│   │   ├── Layout/
│   │   ├── Table/
│   │   └── ...
│   ├── pages/            # 页面组件
│   │   ├── Login/
│   │   ├── Dashboard/
│   │   ├── Transactions/
│   │   └── ...
│   ├── hooks/            # 自定义Hooks
│   ├── utils/            # 工具函数
│   ├── types/            # TypeScript类型定义
│   ├── App.tsx
│   └── main.tsx
├── public/
├── package.json
└── vite.config.ts
```

### 4. API客户端配置

创建 `src/api/client.ts`:

```typescript
import axios from 'axios';

const API_BASE_URL = 'http://localhost/api.php';

const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
  withCredentials: true,
});

// 请求拦截器 - 添加认证token
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// 响应拦截器 - 统一处理错误
apiClient.interceptors.response.use(
  (response) => {
    // 统一响应格式处理
    if (response.data.success !== undefined) {
      return response.data;
    }
    return response;
  },
  (error) => {
    if (error.response?.status === 401) {
      // 未授权，清除token并跳转登录
      localStorage.removeItem('token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default apiClient;
```

### 5. 认证API示例

创建 `src/api/auth.ts`:

```typescript
import apiClient from './client';

export interface LoginRequest {
  username: string;
  password: string;
}

export interface LoginResponse {
  success: boolean;
  data: {
    token: string;
    user: {
      id: number;
      username: string;
      display_name: string;
      role_key: string;
    };
  };
}

export const authApi = {
  login: async (data: LoginRequest): Promise<LoginResponse> => {
    const response = await apiClient.post('?r=auth/login', data);
    if (response.success && response.data.token) {
      localStorage.setItem('token', response.data.token);
    }
    return response;
  },

  logout: async (): Promise<void> => {
    await apiClient.post('?r=auth/logout');
    localStorage.removeItem('token');
  },

  me: async () => {
    return apiClient.get('?r=auth/me');
  },
};
```

### 6. 状态管理示例（Zustand）

创建 `src/store/authStore.ts`:

```typescript
import { create } from 'zustand';
import { authApi } from '../api/auth';

interface User {
  id: number;
  username: string;
  display_name: string;
  role_key: string;
}

interface AuthState {
  user: User | null;
  token: string | null;
  loading: boolean;
  login: (username: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
  checkAuth: () => Promise<void>;
}

export const useAuthStore = create<AuthState>((set) => ({
  user: null,
  token: localStorage.getItem('token'),
  loading: false,

  login: async (username: string, password: string) => {
    set({ loading: true });
    try {
      const response = await authApi.login({ username, password });
      set({
        user: response.data.user,
        token: response.data.token,
        loading: false,
      });
    } catch (error) {
      set({ loading: false });
      throw error;
    }
  },

  logout: async () => {
    await authApi.logout();
    set({ user: null, token: null });
  },

  checkAuth: async () => {
    const token = localStorage.getItem('token');
    if (!token) return;
    
    set({ loading: true });
    try {
      const response = await authApi.me();
      set({ user: response.data, token, loading: false });
    } catch (error) {
      localStorage.removeItem('token');
      set({ user: null, token: null, loading: false });
    }
  },
}));
```

### 7. 路由配置示例

```typescript
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { useAuthStore } from './store/authStore';
import Login from './pages/Login';
import Dashboard from './pages/Dashboard';
import Transactions from './pages/Transactions';

function PrivateRoute({ children }: { children: JSX.Element }) {
  const { user, loading } = useAuthStore();
  
  if (loading) return <div>Loading...</div>;
  if (!user) return <Navigate to="/login" />;
  
  return children;
}

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/login" element={<Login />} />
        <Route
          path="/dashboard"
          element={
            <PrivateRoute>
              <Dashboard />
            </PrivateRoute>
          }
        />
        <Route
          path="/transactions"
          element={
            <PrivateRoute>
              <Transactions />
            </PrivateRoute>
          }
        />
        <Route path="/" element={<Navigate to="/dashboard" />} />
      </Routes>
    </BrowserRouter>
  );
}
```

## 环境变量配置

创建 `.env` 文件：

```env
VITE_API_BASE_URL=http://localhost/api.php
VITE_APP_TITLE=Teah Space 管理系统
```

在代码中使用：

```typescript
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;
```

## CORS配置

确保后端API已配置CORS（已在 `app/core/Response.php` 中配置）。

前端开发服务器默认运行在：
- Vite: `http://localhost:5173`
- Create React App: `http://localhost:3000`
- Vue CLI: `http://localhost:8080`

## 开发命令

```bash
# 启动开发服务器
npm run dev

# 构建生产版本
npm run build

# 预览生产构建
npm run preview
```

## 部署

### 开发环境
前端开发服务器和后端API可以分别运行：
- 前端: `http://localhost:5173`
- 后端: `http://localhost/api.php`

### 生产环境
1. 构建前端：`npm run build`
2. 将 `dist` 目录部署到Web服务器（Nginx/Apache）
3. 配置反向代理，将 `/api.php` 请求转发到PHP后端

### Nginx配置示例

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/frontend/dist;
    index index.html;

    # 前端路由
    location / {
        try_files $uri $uri/ /index.html;
    }

    # API代理
    location /api.php {
        proxy_pass http://localhost:80;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

## 下一步

1. 根据选择的框架创建完整的页面组件
2. 实现所有API接口调用
3. 添加国际化支持（i18n）
4. 实现文件上传功能
5. 添加错误处理和加载状态
6. 优化性能和用户体验
