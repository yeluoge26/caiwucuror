@echo off
chcp 65001 >nul
echo ========================================
echo   推送到 GitHub
echo ========================================
echo.
echo ⚠️  重要提示：
echo GitHub 已不再支持使用密码进行身份验证
echo 请使用 Personal Access Token
echo.
echo 如果没有 Token，请先创建：
echo 1. 访问: https://github.com/settings/tokens
echo 2. 生成新的 token (classic)
echo 3. 复制 token
echo.
pause

echo.
echo 正在推送到 GitHub...
echo.

git push -u origin main

if %errorLevel% equ 0 (
    echo.
    echo [✓] 推送成功！
    echo.
    echo 访问仓库: https://github.com/yeluoge26/caiwucuror
) else (
    echo.
    echo [错误] 推送失败
    echo.
    echo 可能的原因：
    echo 1. 需要输入用户名和密码（请使用 Token）
    echo 2. 仓库不存在，请先在 GitHub 创建仓库
    echo 3. 网络连接问题
    echo.
    echo 手动推送：
    echo git push -u origin main
)

echo.
pause
