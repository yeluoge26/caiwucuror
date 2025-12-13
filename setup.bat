@echo off
chcp 65001 >nul
echo ========================================
echo   财务系统 Windows 配置脚本
echo ========================================
echo.

:: 检测 XAMPP
set XAMPP_PATH=
if exist "C:\xampp\php\php.exe" (
    set XAMPP_PATH=C:\xampp
) else if exist "D:\xampp\php\php.exe" (
    set XAMPP_PATH=D:\xampp
) else (
    echo [错误] 未找到 XAMPP 安装
    echo 请先安装 XAMPP: https://www.apachefriends.org/download.html
    pause
    exit /b 1
)

echo [✓] 找到 XAMPP: %XAMPP_PATH%
echo.

:: 检测 WAMP
set WAMP_PATH=
if exist "C:\wamp64\bin\php\php8.1.0\php.exe" (
    set WAMP_PATH=C:\wamp64
    set PHP_PATH=C:\wamp64\bin\php\php8.1.0
) else if exist "C:\wamp\bin\php\php8.1.0\php.exe" (
    set WAMP_PATH=C:\wamp
    set PHP_PATH=C:\wamp\bin\php\php8.1.0
)

if defined WAMP_PATH (
    echo [✓] 找到 WAMP: %WAMP_PATH%
    echo.
)

:: 设置 PHP 路径
if defined XAMPP_PATH (
    set PHP_EXE=%XAMPP_PATH%\php\php.exe
    set MYSQL_EXE=%XAMPP_PATH%\mysql\bin\mysql.exe
) else if defined WAMP_PATH (
    set PHP_EXE=%PHP_PATH%\php.exe
    set MYSQL_EXE=%WAMP_PATH%\bin\mysql\mysql8.0.xx\bin\mysql.exe
) else (
    :: 尝试系统 PATH
    php -v >nul 2>&1
    if %errorLevel% equ 0 (
        set PHP_EXE=php
        set MYSQL_EXE=mysql
    ) else (
        echo [错误] 未找到 PHP，请先安装 XAMPP 或 WAMP
        pause
        exit /b 1
    )
)

echo [1/4] 检测 PHP...
"%PHP_EXE%" -v
if %errorLevel% neq 0 (
    echo [错误] PHP 无法运行
    pause
    exit /b 1
)

echo.
echo [2/4] 检测 MySQL...
"%MYSQL_EXE%" --version >nul 2>&1
if %errorLevel% neq 0 (
    echo [警告] MySQL 命令行工具未找到，但可能服务正在运行
) else (
    "%MYSQL_EXE%" --version
)

echo.
echo [3/4] 创建数据库...
echo 请输入 MySQL root 密码（如果没有密码，直接按回车）:
set /p MYSQL_PASS=密码: 

if "%MYSQL_PASS%"=="" (
    set MYSQL_CMD=%MYSQL_EXE% -u root
) else (
    set MYSQL_CMD=%MYSQL_EXE% -u root -p%MYSQL_PASS%
)

echo.
echo 正在创建数据库 coffee_finance...
echo CREATE DATABASE IF NOT EXISTS coffee_finance DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; | "%MYSQL_CMD%"
if %errorLevel% neq 0 (
    echo [错误] 无法连接到 MySQL
    echo 请确保：
    echo 1. MySQL 服务正在运行
    echo 2. 密码正确
    echo 3. 如果使用 XAMPP，请通过 XAMPP Control Panel 启动 MySQL
    pause
    exit /b 1
)

echo [✓] 数据库创建成功
echo.

echo [4/4] 导入数据库结构...
if exist "database\schema.sql" (
    echo 正在导入 schema.sql...
    "%MYSQL_CMD%" coffee_finance < database\schema.sql
    if %errorLevel% equ 0 (
        echo [✓] 数据库结构导入成功
    ) else (
        echo [错误] 导入失败，请检查 SQL 文件
    )
) else (
    echo [错误] 找不到 database\schema.sql 文件
)

echo.
echo ========================================
echo   配置完成！
echo ========================================
echo.
echo 下一步：
echo 1. 确保 Apache 服务正在运行
echo 2. 确保 MySQL 服务正在运行
echo 3. 访问: http://localhost/public/index.php?r=auth/login
echo 4. 默认账号: admin / admin123
echo.
echo 如果使用 XAMPP：
echo   - 打开 XAMPP Control Panel
echo   - 启动 Apache 和 MySQL
echo.
echo 如果使用 WAMP：
echo   - 点击系统托盘图标
echo   - 启动所有服务
echo.
pause
