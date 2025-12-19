#!/bin/bash

# macOS 安装和运行脚本
# 财务管理系统 - macOS 快速开始

set -e

echo "=========================================="
echo "财务管理系统 - macOS 安装脚本"
echo "=========================================="
echo ""

# 检查 Homebrew
if ! command -v brew &> /dev/null; then
    echo "❌ 未检测到 Homebrew，请先安装 Homebrew："
    echo "   /bin/bash -c \"\$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)\""
    exit 1
fi

echo "✅ Homebrew 已安装"
echo ""

# 安装 PHP
if ! command -v php &> /dev/null; then
    echo "📦 正在安装 PHP..."
    brew install php
    echo "✅ PHP 安装完成"
else
    PHP_VERSION=$(php -v | head -n 1 | cut -d ' ' -f 2 | cut -d '.' -f 1,2)
    echo "✅ PHP 已安装 (版本: $PHP_VERSION)"
fi
echo ""

# 安装 MySQL
if ! command -v mysql &> /dev/null; then
    echo "📦 正在安装 MySQL..."
    brew install mysql
    echo "✅ MySQL 安装完成"
    echo ""
    echo "⚠️  请启动 MySQL 服务："
    echo "   brew services start mysql"
    echo ""
    read -p "是否现在启动 MySQL 服务？(y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        brew services start mysql
        echo "✅ MySQL 服务已启动"
        sleep 3
    fi
else
    echo "✅ MySQL 已安装"
    # 检查 MySQL 服务是否运行
    if ! pgrep -x mysqld > /dev/null; then
        echo "⚠️  MySQL 服务未运行，正在启动..."
        brew services start mysql
        sleep 3
    fi
    echo "✅ MySQL 服务正在运行"
fi
echo ""

# 创建数据库
echo "📊 正在创建数据库..."
mysql -u root -e "CREATE DATABASE IF NOT EXISTS coffee_finance DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;" 2>/dev/null || {
    echo "⚠️  无法连接到 MySQL，请检查："
    echo "   1. MySQL 服务是否运行：brew services list"
    echo "   2. 数据库密码是否正确（如果需要密码，请修改 app/config/db.php）"
    echo ""
    read -p "请输入 MySQL root 密码（如果没有密码直接回车）: " -s MYSQL_PASS
    echo ""
    if [ -z "$MYSQL_PASS" ]; then
        mysql -u root -e "CREATE DATABASE IF NOT EXISTS coffee_finance DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
    else
        mysql -u root -p"$MYSQL_PASS" -e "CREATE DATABASE IF NOT EXISTS coffee_finance DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
    fi
}
echo "✅ 数据库创建完成"
echo ""

# 导入数据库结构
echo "📥 正在导入数据库结构..."
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
if [ -z "$MYSQL_PASS" ]; then
    mysql -u root coffee_finance < "$SCRIPT_DIR/database/schema.sql" 2>/dev/null || {
        echo "⚠️  导入失败，请手动执行："
        echo "   mysql -u root coffee_finance < database/schema.sql"
    }
else
    mysql -u root -p"$MYSQL_PASS" coffee_finance < "$SCRIPT_DIR/database/schema.sql" 2>/dev/null || {
        echo "⚠️  导入失败，请手动执行："
        echo "   mysql -u root -p coffee_finance < database/schema.sql"
    }
fi
echo "✅ 数据库结构导入完成"
echo ""

# 检查数据库配置文件
if [ ! -f "$SCRIPT_DIR/app/config/db.php" ]; then
    echo "📝 正在创建数据库配置文件..."
    cp "$SCRIPT_DIR/app/config/db.php.example" "$SCRIPT_DIR/app/config/db.php"
    echo "✅ 配置文件已创建，请根据需要修改 app/config/db.php"
    echo ""
fi

echo "=========================================="
echo "✅ 安装完成！"
echo "=========================================="
echo ""
echo "📋 默认登录信息："
echo "   用户名: admin"
echo "   密码: admin123"
echo ""
echo "🚀 启动服务器："
echo "   cd $SCRIPT_DIR"
echo "   php -S localhost:8000 -t public"
echo ""
echo "🌐 访问地址："
echo "   http://localhost:8000/index.php?r=auth/login"
echo ""

