# Git 认证问题修复指南

## 问题说明

GitHub 已经不再支持密码认证，需要使用 Personal Access Token (PAT) 或 SSH 密钥。

## 解决方案

### 方案1：使用 Personal Access Token（推荐，简单快速）

1. **创建 Personal Access Token**
   - 访问：https://github.com/settings/tokens
   - 点击 "Generate new token" -> "Generate new token (classic)"
   - 设置名称（如：caiwucuror-access）
   - 选择过期时间（建议选择较长时间，如 90 天或 1 年）
   - 勾选权限：至少需要 `repo` 权限
   - 点击 "Generate token"
   - **重要：复制生成的 token（只显示一次）**

2. **使用 Token 进行认证**
   
   方法A：在推送时输入 token
   ```bash
   git push
   # Username: yeluoge26
   # Password: [粘贴你的 token，不是密码]
   ```
   
   方法B：将 token 保存到 Git 凭据存储
   ```bash
   # 设置 Git 凭据存储
   git config --global credential.helper store
   
   # 然后推送，输入用户名和 token
   git push
   # Username: yeluoge26
   # Password: [粘贴你的 token]
   ```
   
   方法C：在 URL 中包含 token（临时使用）
   ```bash
   git remote set-url origin https://yeluoge26:[YOUR_TOKEN]@github.com/yeluoge26/caiwucuror.git
   git push
   ```

### 方案2：使用 SSH 密钥（推荐，更安全）

1. **检查是否已有 SSH 密钥**
   ```bash
   ls -al ~/.ssh
   ```
   如果看到 `id_rsa` 和 `id_rsa.pub` 或 `id_ed25519` 和 `id_ed25519.pub`，说明已有密钥。

2. **如果没有，生成新的 SSH 密钥**
   ```bash
   ssh-keygen -t ed25519 -C "your_email@example.com"
   # 按 Enter 使用默认路径
   # 可以设置密码或直接按 Enter 跳过
   ```

3. **复制公钥**
   ```bash
   cat ~/.ssh/id_ed25519.pub
   # 或
   cat ~/.ssh/id_rsa.pub
   ```
   复制输出的内容。

4. **添加到 GitHub**
   - 访问：https://github.com/settings/keys
   - 点击 "New SSH key"
   - Title: 输入一个名称（如：semon-server）
   - Key: 粘贴刚才复制的公钥
   - 点击 "Add SSH key"

5. **测试 SSH 连接**
   ```bash
   ssh -T git@github.com
   ```
   应该看到：`Hi yeluoge26! You've successfully authenticated...`

6. **更改远程 URL 为 SSH**
   ```bash
   git remote set-url origin git@github.com:yeluoge26/caiwucuror.git
   ```

7. **推送**
   ```bash
   git push
   ```

### 方案3：使用 GitHub CLI（如果已安装）

```bash
gh auth login
# 按照提示操作
git push
```

## 快速修复（推荐使用方案1的方法B）

```bash
# 1. 设置 Git 凭据存储
git config --global credential.helper store

# 2. 推送（会提示输入用户名和 token）
git push
# Username: yeluoge26
# Password: [粘贴你的 Personal Access Token]

# 3. 之后就不需要再输入了
```

## 注意事项

1. **Personal Access Token 安全**
   - Token 等同于密码，请妥善保管
   - 不要在代码中硬编码 token
   - 如果泄露，立即在 GitHub 设置中删除并重新生成

2. **Token 权限**
   - 对于私有仓库，需要 `repo` 权限
   - 对于公开仓库，可以使用 `public_repo` 权限

3. **Token 过期**
   - 如果 token 过期，需要重新生成并更新

4. **SSH vs HTTPS**
   - SSH 更安全，不需要每次输入密码
   - HTTPS + Token 更简单，适合临时使用

## 故障排查

### 如果仍然提示认证失败

1. **检查远程 URL**
   ```bash
   git remote -v
   ```
   确保 URL 正确。

2. **清除缓存的凭据**
   ```bash
   git credential-cache exit
   # 或
   git credential reject https://github.com
   ```

3. **重新设置远程 URL**
   ```bash
   git remote set-url origin https://github.com/yeluoge26/caiwucuror.git
   ```

4. **检查网络连接**
   ```bash
   ping github.com
   ```

