@echo off
chcp 65001 >nul
echo ========================================
echo   启动 Web 服务器
echo ========================================
echo.

:: 检测 XAMPP
if exist "C:\xampp\xampp-control.exe" (
    echo [✓] 找到 XAMPP
    echo 正在启动 XAMPP Control Panel...
    start "" "C:\xampp\xampp-control.exe"
    echo.
    echo 请在 XAMPP Control Panel 中：
    echo 1. 点击 Apache 的 "Start" 按钮
    echo 2. 点击 MySQL 的 "Start" 按钮
    echo 3. 等待服务启动完成
    echo.
    echo 然后访问: http://localhost/public/index.php?r=auth/login
    pause
    exit /b 0
)

if exist "D:\xampp\xampp-control.exe" (
    echo [✓] 找到 XAMPP
    echo 正在启动 XAMPP Control Panel...
    start "" "D:\xampp\xampp-control.exe"
    echo.
    echo 请在 XAMPP Control Panel 中：
    echo 1. 点击 Apache 的 "Start" 按钮
    echo 2. 点击 MySQL 的 "Start" 按钮
    echo 3. 等待服务启动完成
    echo.
    echo 然后访问: http://localhost/public/index.php?r=auth/login
    pause
    exit /b 0
)

:: 检测 WAMP
if exist "C:\wamp64\wampmanager.exe" (
    echo [✓] 找到 WAMP
    echo 正在启动 WAMP Manager...
    start "" "C:\wamp64\wampmanager.exe"
    echo.
    echo 请点击系统托盘中的 WAMP 图标：
    echo 1. 选择 "Start All Services"
    echo 2. 等待服务启动完成
    echo.
    echo 然后访问: http://localhost/public/index.php?r=auth/login
    pause
    exit /b 0
)

if exist "C:\wamp\wampmanager.exe" (
    echo [✓] 找到 WAMP
    echo 正在启动 WAMP Manager...
    start "" "C:\wamp\wampmanager.exe"
    echo.
    echo 请点击系统托盘中的 WAMP 图标：
    echo 1. 选择 "Start All Services"
    echo 2. 等待服务启动完成
    echo.
    echo 然后访问: http://localhost/public/index.php?r=auth/login
    pause
    exit /b 0
)

:: 尝试直接启动 Apache（如果已安装）
httpd -v >nul 2>&1
if %errorLevel% equ 0 (
    echo [✓] 找到 Apache
    echo 正在启动 Apache...
    net start Apache2.4 >nul 2>&1
    if %errorLevel% equ 0 (
        echo [✓] Apache 已启动
    ) else (
        echo [警告] 无法通过服务启动 Apache，请手动启动
    )
) else (
    echo [错误] 未找到 XAMPP 或 WAMP
    echo.
    echo 请先安装 XAMPP：
    echo 1. 下载: https://www.apachefriends.org/download.html
    echo 2. 运行 install_windows.bat 进行安装
    echo.
)

pause
