@echo off
chcp 65001 >nul
echo ========================================
echo   财务系统 Windows 环境检测和安装工具
echo ========================================
echo.

:: 检查管理员权限
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo [错误] 请以管理员身份运行此脚本！
    echo 右键点击此文件，选择"以管理员身份运行"
    pause
    exit /b 1
)

echo [1/5] 检测 PHP...
php -v >nul 2>&1
if %errorLevel% equ 0 (
    echo [✓] PHP 已安装
    php -v
) else (
    echo [✗] PHP 未安装
    set PHP_INSTALLED=0
)

echo.
echo [2/5] 检测 MySQL...
mysql --version >nul 2>&1
if %errorLevel% equ 0 (
    echo [✓] MySQL 已安装
    mysql --version
) else (
    echo [✗] MySQL 未安装
    set MYSQL_INSTALLED=0
)

echo.
echo [3/5] 检测 Apache...
httpd -v >nul 2>&1
if %errorLevel% equ 0 (
    echo [✓] Apache 已安装
    httpd -v
) else (
    echo [✗] Apache 未安装
    set APACHE_INSTALLED=0
)

echo.
echo [4/5] 检测 XAMPP...
if exist "C:\xampp\xampp-control.exe" (
    echo [✓] 检测到 XAMPP 安装
    set XAMPP_FOUND=1
    set XAMPP_PATH=C:\xampp
) else if exist "D:\xampp\xampp-control.exe" (
    echo [✓] 检测到 XAMPP 安装
    set XAMPP_FOUND=1
    set XAMPP_PATH=D:\xampp
) else (
    echo [✗] 未检测到 XAMPP
    set XAMPP_FOUND=0
)

echo.
echo [5/5] 检测 WAMP...
if exist "C:\wamp64\wampmanager.exe" (
    echo [✓] 检测到 WAMP 安装
    set WAMP_FOUND=1
    set WAMP_PATH=C:\wamp64
) else if exist "C:\wamp\wampmanager.exe" (
    echo [✓] 检测到 WAMP 安装
    set WAMP_FOUND=1
    set WAMP_PATH=C:\wamp
) else (
    echo [✗] 未检测到 WAMP
    set WAMP_FOUND=0
)

echo.
echo ========================================
echo   检测结果汇总
echo ========================================
if defined PHP_INSTALLED (
    echo PHP: 未安装
) else (
    echo PHP: 已安装
)

if defined MYSQL_INSTALLED (
    echo MySQL: 未安装
) else (
    echo MySQL: 已安装
)

if defined APACHE_INSTALLED (
    echo Apache: 未安装
) else (
    echo Apache: 已安装
)

if %XAMPP_FOUND%==1 (
    echo XAMPP: 已找到 (%XAMPP_PATH%)
)

if %WAMP_FOUND%==1 (
    echo WAMP: 已找到 (%WAMP_PATH%)
)

echo.
if defined PHP_INSTALLED set NEED_INSTALL=1
if defined MYSQL_INSTALLED set NEED_INSTALL=1
if defined APACHE_INSTALLED set NEED_INSTALL=1

if defined NEED_INSTALL (
    set NEED_INSTALL=1
)

if "%NEED_INSTALL%"=="1" (
    echo ========================================
    echo   需要安装开发环境
    echo ========================================
    echo.
    echo 推荐安装 XAMPP（最简单的方式）
    echo.
    echo 请选择安装方式：
    echo 1. 自动下载 XAMPP（推荐）
    echo 2. 手动安装指南
    echo 3. 使用 Chocolatey 安装（需要先安装 Chocolatey）
    echo 4. 退出
    echo.
    set /p choice=请输入选项 (1-4): 
    
    if "%choice%"=="1" (
        call :install_xampp
    ) else if "%choice%"=="2" (
        call :manual_install
    ) else if "%choice%"=="3" (
        call :choco_install
    ) else (
        echo 退出安装
        exit /b 0
    )
) else (
    echo ========================================
    echo   ✓ 环境检测通过！
    echo ========================================
    echo.
    echo 下一步：
    echo 1. 确保 MySQL 服务正在运行
    echo 2. 确保 Apache 服务正在运行
    echo 3. 运行 setup.bat 进行系统配置
    echo.
)

pause
exit /b 0

:install_xampp
echo.
echo ========================================
echo   安装 XAMPP
echo ========================================
echo.
echo 请选择安装方式：
echo 1. 使用 PowerShell 自动下载（推荐）
echo 2. 手动下载安装
echo 3. 返回
echo.
set /p install_choice=请输入选项 (1-3): 

if "%install_choice%"=="1" (
    echo.
    echo 正在启动 PowerShell 下载脚本...
    powershell -ExecutionPolicy Bypass -File "%~dp0download_xampp.ps1"
    goto :end
) else if "%install_choice%"=="2" (
    echo.
    echo 正在打开 XAMPP 下载页面...
    start https://www.apachefriends.org/download.html
    echo.
    echo 请按照以下步骤操作：
    echo 1. 下载 XAMPP for Windows (PHP 8.1)
    echo 2. 运行安装程序
    echo 3. 安装到 C:\xampp（或自定义路径）
    echo 4. 选择组件：Apache、MySQL、PHP、phpMyAdmin
    echo 5. 安装完成后，重新运行此脚本
    echo.
    pause
    goto :end
) else (
    goto :end
)

:end
exit /b 0

:manual_install
echo.
echo ========================================
echo   手动安装指南
echo ========================================
echo.
echo 方式1: 安装 XAMPP（推荐）
echo   - 下载地址: https://www.apachefriends.org/download.html
echo   - 包含: Apache + MySQL + PHP + phpMyAdmin
echo   - 一键安装，最简单
echo.
echo 方式2: 安装 WAMP
echo   - 下载地址: https://www.wampserver.com/
echo   - 包含: Apache + MySQL + PHP
echo.
echo 方式3: 单独安装各组件
echo   - PHP: https://windows.php.net/download/
echo   - MySQL: https://dev.mysql.com/downloads/installer/
echo   - Apache: https://httpd.apache.org/download.cgi
echo.
echo 安装完成后，请重新运行此脚本进行检测
echo.
pause
exit /b 0

:choco_install
echo.
echo ========================================
echo   使用 Chocolatey 安装
echo ========================================
echo.
choco --version >nul 2>&1
if %errorLevel% neq 0 (
    echo Chocolatey 未安装，正在安装...
    powershell -NoProfile -ExecutionPolicy Bypass -Command "Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))"
    echo.
    echo Chocolatey 安装完成，请重新运行此脚本
    pause
    exit /b 0
)

echo 正在安装 XAMPP...
choco install xampp-81 -y
echo.
echo 安装完成！请重新运行此脚本
pause
exit /b 0
