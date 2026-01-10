# 前端项目

基于 React + TypeScript + Vite 构建的现代化前端应用。

## 技术栈

- **React 18** - UI框架
- **TypeScript** - 类型安全
- **Vite** - 构建工具
- **React Router** - 路由
- **Axios** - HTTP客户端
- **Zustand** - 状态管理
- **Ant Design** - UI组件库

## 快速开始

### 安装依赖

```bash
npm install
```

### 启动开发服务器

```bash
npm run dev
```

访问 http://localhost:5173

### 构建生产版本

```bash
npm run build
```

### 预览生产构建

```bash
npm run preview
```

## 项目结构

```
frontend/
├── src/
│   ├── api/              # API调用
│   │   ├── client.ts     # Axios实例配置
│   │   └── auth.ts       # 认证相关API
│   ├── store/            # 状态管理
│   │   └── authStore.ts  # 认证状态
│   ├── components/       # 公共组件
│   │   └── Layout.tsx    # 布局组件
│   ├── pages/            # 页面组件
│   │   ├── Login.tsx     # 登录页
│   │   └── Dashboard.tsx # 仪表盘
│   ├── App.tsx           # 根组件
│   └── main.tsx          # 入口文件
├── public/               # 静态资源
├── index.html            # HTML模板
├── vite.config.ts        # Vite配置
├── tsconfig.json         # TypeScript配置
└── package.json          # 依赖配置
```

## 环境变量

创建 `.env` 文件：

```env
VITE_API_BASE_URL=http://localhost/api.php
VITE_APP_TITLE=Teah Space 管理系统
```

## API集成

所有API调用都在 `src/api/` 目录下，使用统一的 `apiClient` 实例。

### 示例：调用API

```typescript
import apiClient from './api/client'

// GET请求
const response = await apiClient.get('?r=transactions/list?page=1&limit=20')

// POST请求
const response = await apiClient.post('?r=transactions/create', {
  type: 'income',
  amount: 100000,
  currency: 'VND'
})
```

## 下一步

1. 实现更多页面组件（交易列表、员工管理、班次管理等）
2. 添加更多API接口调用
3. 实现国际化（i18n）
4. 添加错误处理和加载状态
5. 优化性能和用户体验
