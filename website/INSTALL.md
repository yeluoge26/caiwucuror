# 安装和运行指南

## 前置要求

在运行此项目之前，您需要先安装 Node.js 和 npm。

### 安装 Node.js

1. 访问 [Node.js 官网](https://nodejs.org/)
2. 下载并安装 LTS 版本（推荐 v18 或更高版本）
3. 安装完成后，重新打开终端/命令行

### 验证安装

安装完成后，在终端中运行以下命令验证：

```bash
node --version
npm --version
```

如果显示版本号，说明安装成功。

## 安装项目依赖

在项目根目录下运行：

```bash
npm install
```

这将安装所有必需的依赖包。

## 运行开发服务器

安装完成后，运行：

```bash
npm run dev
```

开发服务器将在 http://localhost:3000 启动。

## 访问网站

- **英文版**: http://localhost:3000/en
- **越南语版**: http://localhost:3000/vi  
- **韩语版**: http://localhost:3000/ko

## 其他命令

- `npm run build` - 构建生产版本
- `npm run start` - 运行生产服务器
- `npm run lint` - 运行代码检查

## 常见问题

### 如果 npm 命令不识别

1. 确保已安装 Node.js
2. 重新启动终端/命令行窗口
3. 检查环境变量 PATH 中是否包含 Node.js 的安装路径

### PowerShell 执行策略错误

如果您在 PowerShell 中看到类似 "无法加载文件...因为在此系统上禁止运行脚本" 的错误：

**临时解决方案（仅对当前会话有效）：**
```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope Process
```

**永久解决方案（推荐）：**
1. 以管理员身份打开 PowerShell
2. 运行以下命令：
```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

或者，您也可以使用命令提示符（CMD）代替 PowerShell，CMD 不受执行策略限制。

### 如果端口 3000 已被占用

Next.js 会自动尝试下一个可用端口（3001, 3002 等）

## 在 aaPanel 上部署

### 前置要求

1. 已安装 aaPanel 的 Linux 服务器
2. 已安装 Nginx（通过 aaPanel 安装）
3. 已安装 Node.js 版本管理器（通过 aaPanel 的 Node.js 版本管理器安装）

### 步骤 1: 安装 Node.js

1. 登录 aaPanel 管理面板
2. 进入 **App Store** → 搜索 **Node.js Version Manager**
3. 安装 Node.js Version Manager
4. 在 Node.js Version Manager 中安装 **Node.js 18.x 或更高版本**（推荐 LTS 版本）

### 步骤 2: 上传项目文件

**方法一：通过 aaPanel 文件管理器**

1. 进入 **File** → **File Manager**
2. 导航到网站根目录（例如：`/www/wwwroot/your-domain.com`）
3. 上传项目文件（可以通过压缩包上传后解压）

**方法二：通过 Git（推荐）**

1. 在 aaPanel 终端中执行：
```bash
cd /www/wwwroot/your-domain.com
git clone <your-repository-url> .
```

**方法三：通过 SFTP**

使用 SFTP 客户端（如 FileZilla）上传项目文件到服务器

### 步骤 3: 安装项目依赖

1. 在 aaPanel 中打开 **Terminal**（终端）
2. 进入项目目录：
```bash
cd /www/wwwroot/your-domain.com
```
3. 安装依赖：
```bash
npm install --production=false
```

> **注意**：`--production=false` 确保安装开发依赖（如 TypeScript），这些是构建项目所必需的。

### 步骤 4: 构建生产版本

在项目目录中运行：
```bash
npm run build
```

构建完成后，会生成 `.next` 文件夹。

### 步骤 5: 使用 PM2 管理进程

1. **安装 PM2**（如果未安装）：
```bash
npm install -g pm2
```

2. **创建 PM2 启动脚本**：
在项目根目录创建 `ecosystem.config.js` 文件：
```javascript
module.exports = {
  apps: [{
    name: 'tech-coffee-website',
    script: 'npm',
    args: 'start',
    cwd: '/www/wwwroot/your-domain.com',
    instances: 1,
    autorestart: true,
    watch: false,
    max_memory_restart: '1G',
    env: {
      NODE_ENV: 'production',
      PORT: 3000
    }
  }]
}
```

> **注意**：将 `/www/wwwroot/your-domain.com` 替换为您的实际项目路径。

3. **启动应用**：
```bash
pm2 start ecosystem.config.js
```

4. **设置 PM2 开机自启**：
```bash
pm2 startup
pm2 save
```

5. **查看应用状态**：
```bash
pm2 status
pm2 logs tech-coffee-website
```

### 步骤 6: 配置 Nginx 反向代理

1. 在 aaPanel 中进入 **Website** → 选择您的网站 → **Settings**
2. 进入 **Configuration** 标签页
3. 在配置文件中添加以下内容（在 `server` 块内）：

```nginx
location / {
    proxy_pass http://localhost:3000;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection 'upgrade';
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_cache_bypass $http_upgrade;
}
```

4. 保存配置并重载 Nginx：
```bash
nginx -t  # 测试配置
nginx -s reload  # 重载配置
```

或者在 aaPanel 中点击 **Reload** 按钮。

### 步骤 7: 配置 SSL 证书（可选但推荐）

1. 在 aaPanel 中进入 **Website** → 选择您的网站 → **SSL**
2. 选择 **Let's Encrypt** 免费证书
3. 填写域名并申请证书
4. 启用 **Force HTTPS**（强制 HTTPS）

### 步骤 8: 验证部署

访问您的域名：
- `https://your-domain.com/en` - 英文版
- `https://your-domain.com/vi` - 越南语版
- `https://your-domain.com/ko` - 韩语版

### 常用 PM2 命令

```bash
pm2 status              # 查看应用状态
pm2 logs tech-coffee-website  # 查看日志
pm2 restart tech-coffee-website  # 重启应用
pm2 stop tech-coffee-website    # 停止应用
pm2 delete tech-coffee-website  # 删除应用
pm2 monit               # 监控面板
```

### 更新部署

当需要更新代码时：

1. **通过 Git 更新**（如果使用 Git）：
```bash
cd /www/wwwroot/your-domain.com
git pull
npm install
npm run build
pm2 restart tech-coffee-website
```

2. **手动更新**：
   - 上传新文件
   - 在终端中执行：
```bash
cd /www/wwwroot/your-domain.com
npm install
npm run build
pm2 restart tech-coffee-website
```

### 故障排查

**如果网站无法访问：**

1. 检查 PM2 进程是否运行：
```bash
pm2 status
```

2. 查看应用日志：
```bash
pm2 logs tech-coffee-website
```

3. 检查端口是否被占用：
```bash
netstat -tulpn | grep 3000
```

4. 检查 Nginx 配置：
```bash
nginx -t
```

5. 检查防火墙设置（确保 80 和 443 端口开放）

**如果构建失败：**

1. 确保安装了所有依赖：
```bash
npm install --production=false
```

2. 检查 Node.js 版本（需要 18+）：
```bash
node --version
```

3. 清理缓存后重新构建：
```bash
rm -rf .next node_modules
npm install
npm run build
```


