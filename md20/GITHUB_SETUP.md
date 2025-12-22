# GitHub 上传指南

## ⚠️ 重要安全提示

**不建议在命令行中直接使用密码！**

GitHub 已经不再支持使用密码进行身份验证。请使用以下方式之一：

### 方式1: 使用 Personal Access Token（推荐）

1. **创建 Token**
   - 访问: https://github.com/settings/tokens
   - 点击 "Generate new token" → "Generate new token (classic)"
   - 设置名称和过期时间
   - 选择权限：至少勾选 `repo`
   - 生成并复制 token

2. **使用 Token 推送**
   ```bash
   git remote set-url origin https://yeluoge26@github.com/yeluoge26/caiwucuror.git
   git push -u origin main
   # 用户名: yeluoge26
   # 密码: 输入刚才复制的 token
   ```

### 方式2: 使用 SSH Key（更安全）

1. **生成 SSH Key**
   ```bash
   ssh-keygen -t ed25519 -C "your_email@example.com"
   ```

2. **添加 SSH Key 到 GitHub**
   - 复制 `~/.ssh/id_ed25519.pub` 内容
   - 访问: https://github.com/settings/keys
   - 点击 "New SSH key"，粘贴并保存

3. **使用 SSH URL**
   ```bash
   git remote set-url origin git@github.com:yeluoge26/caiwucuror.git
   git push -u origin main
   ```

## 当前状态

代码已经准备好上传，执行以下命令：

```bash
cd d:\code\caiwucuror
git push -u origin main
```

如果提示输入密码，请使用 Personal Access Token。

## 已排除的文件

以下文件不会上传到 GitHub（已在 .gitignore 中）：
- `app/config/db.php` - 包含数据库密码
- `public/uploads/*` - 上传的文件
- 日志和临时文件

## 首次使用需要

1. 在 GitHub 创建仓库（如果还没有）
   - 访问: https://github.com/new
   - 仓库名: `caiwucuror`
   - 设置为 Private（如果包含敏感信息）

2. 使用 Token 推送代码
