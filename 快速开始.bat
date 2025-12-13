@echo off
chcp 65001 >nul
title 财务系统 - 快速开始
color 0A

echo.
echo ╔══════════════════════════════════════════════════════════╗
echo ║                                                          ║
echo ║           财务管理系统 - Windows 快速开始向导            ║
echo ║                                                          ║
echo ╚══════════════════════════════════════════════════════════╝
echo.
echo.

:menu
echo ┌──────────────────────────────────────────────────────────┐
echo │  请选择操作：                                            │
echo ├──────────────────────────────────────────────────────────┤
echo │  1. 检测环境（检查是否已安装 PHP/MySQL/Apache）          │
echo │  2. 安装 XAMPP（如果没有安装开发环境）                   │
echo │  3. 配置数据库（创建数据库和导入数据）                   │
echo │  4. 启动服务器（启动 Apache 和 MySQL）                   │
echo │  5. 访问系统（在浏览器中打开登录页面）                   │
echo │  6. 运行测试（检查系统配置）                             │
echo │  0. 退出                                                 │
echo └──────────────────────────────────────────────────────────┘
echo.
set /p choice=请输入选项 (0-6): 

if "%choice%"=="1" goto check_env
if "%choice%"=="2" goto install_xampp
if "%choice%"=="3" goto setup_db
if "%choice%"=="4" goto start_server
if "%choice%"=="5" goto open_browser
if "%choice%"=="6" goto run_test
if "%choice%"=="0" goto end
goto menu

:check_env
cls
echo.
echo [正在检测环境...]
echo.
call install_windows.bat
echo.
pause
cls
goto menu

:install_xampp
cls
echo.
echo [准备安装 XAMPP...]
echo.
if exist "download_xampp.ps1" (
    echo 正在启动 PowerShell 下载脚本...
    powershell -ExecutionPolicy Bypass -File "%~dp0download_xampp.ps1"
) else (
    echo 正在打开 XAMPP 下载页面...
    start https://www.apachefriends.org/download.html
    echo.
    echo 请按照以下步骤操作：
    echo 1. 下载 XAMPP for Windows (PHP 8.1)
    echo 2. 运行安装程序
    echo 3. 安装完成后，返回此菜单选择"配置数据库"
    echo.
)
pause
cls
goto menu

:setup_db
cls
echo.
echo [配置数据库...]
echo.
call setup.bat
echo.
pause
cls
goto menu

:start_server
cls
echo.
echo [启动服务器...]
echo.
call start_server.bat
echo.
pause
cls
goto menu

:open_browser
cls
echo.
echo [打开浏览器...]
echo.
start http://localhost/public/index.php?r=auth/login
echo.
echo 如果页面无法打开，请确保：
echo 1. Apache 服务正在运行
echo 2. MySQL 服务正在运行
echo 3. 已运行"配置数据库"选项
echo.
pause
cls
goto menu

:run_test
cls
echo.
echo [运行系统测试...]
echo.
start http://localhost/public/test.php
echo.
echo 测试页面已在浏览器中打开
echo.
pause
cls
goto menu

:end
cls
echo.
echo 感谢使用财务管理系统！
echo.
echo 如有问题，请查看文档：
echo - README_WINDOWS.md
echo - WINDOWS_INSTALL.md
echo - TEST_GUIDE.md
echo.
pause
exit /b 0
