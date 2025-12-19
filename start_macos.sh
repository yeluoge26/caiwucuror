#!/bin/bash

# macOS 启动脚本
# 启动 PHP 内置服务器

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "=========================================="
echo "财务管理系统 - 启动服务器"
echo "=========================================="
echo ""
echo "📂 项目目录: $SCRIPT_DIR"
echo "🌐 访问地址: http://localhost:8000/index.php?r=auth/login"
echo ""
echo "按 Ctrl+C 停止服务器"
echo "=========================================="
echo ""

cd "$SCRIPT_DIR"
php -S localhost:8000 -t public

