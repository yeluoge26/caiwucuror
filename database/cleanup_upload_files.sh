#!/bin/bash
# ============================================
# 清理上传文件脚本
# 用途：删除所有测试上传的文件，准备部署到生产环境
# 警告：此脚本会删除所有上传的文件，请谨慎使用！
# ============================================

# 项目根目录（请根据实际情况修改）
PROJECT_DIR="/www/wwwroot/test.TECHCOFFEEDANANG.COM/caiwucuror"

# 上传目录
UPLOAD_DIR="$PROJECT_DIR/public/uploads"

echo "============================================"
echo "清理上传文件脚本"
echo "============================================"
echo ""
echo "项目目录: $PROJECT_DIR"
echo "上传目录: $UPLOAD_DIR"
echo ""

# 检查目录是否存在
if [ ! -d "$UPLOAD_DIR" ]; then
    echo "错误: 上传目录不存在: $UPLOAD_DIR"
    exit 1
fi

# 显示当前文件统计
echo "当前上传文件统计:"
find "$UPLOAD_DIR" -type f | wc -l | xargs echo "  文件总数:"
du -sh "$UPLOAD_DIR" | awk '{print "  总大小: " $1}'
echo ""

# 确认删除
read -p "确定要删除所有上传的文件吗？(yes/no): " confirm
if [ "$confirm" != "yes" ]; then
    echo "操作已取消"
    exit 0
fi

# 删除所有文件（保留目录结构）
echo "正在删除文件..."
find "$UPLOAD_DIR" -type f -delete

# 重新创建必要的目录结构
echo "重新创建目录结构..."
mkdir -p "$UPLOAD_DIR/inspections"
chmod 755 "$UPLOAD_DIR"
chmod 755 "$UPLOAD_DIR/inspections"

# 验证清理结果
echo ""
echo "清理后统计:"
find "$UPLOAD_DIR" -type f | wc -l | xargs echo "  文件总数:"
du -sh "$UPLOAD_DIR" | awk '{print "  总大小: " $1}'
echo ""

echo "============================================"
echo "清理完成！"
echo "============================================"



