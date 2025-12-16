# 移动端图片上传功能修复说明

## 📋 问题描述

移动端图片上传失败的问题已修复，现在支持完整的图片上传功能。

## ✅ 修复内容

### 1. 添加图片上传功能

**新增文件**:
- `app/models/Attachment.php` - 附件模型

**修改文件**:
- `app/controllers/TransactionController.php` - 添加图片上传处理
- `app/views/transactions/create.php` - 添加图片上传表单
- `app/views/transactions/edit.php` - 添加图片上传表单
- `app/views/transactions/view.php` - 显示已上传的图片

### 2. 移动端优化

**修复的问题**:
- ✅ 表单添加 `enctype="multipart/form-data"` 属性
- ✅ 文件输入框字体大小优化（防止iOS缩放）
- ✅ 图片预览网格布局响应式优化
- ✅ 触摸友好的按钮大小

**移动端特性**:
- 支持多图片选择（最多5张）
- 实时图片预览
- 可删除已选择的图片
- 文件大小和类型验证

### 3. 功能特性

**上传限制**:
- 文件类型：仅支持图片（JPG、PNG、GIF、WebP）
- 文件大小：单个文件最大5MB
- 文件数量：最多5张图片
- 存储位置：`public/uploads/`

**安全特性**:
- 文件类型验证
- 文件大小验证
- 唯一文件名生成（防止覆盖）
- 权限控制（只能删除自己上传的或owner/accountant可删除）

**用户体验**:
- 实时图片预览
- 可删除已选择的图片
- 显示已选择文件数量
- 友好的错误提示

---

## 🔧 技术实现

### 后端处理

```php
// 文件上传处理
private static function handleFileUploads($transactionId, $userId) {
  // 1. 验证上传目录
  // 2. 验证文件类型和大小
  // 3. 生成唯一文件名
  // 4. 移动文件到上传目录
  // 5. 保存到数据库
}
```

### 前端处理

```javascript
// 图片选择和预览
function handleFileSelect(event) {
  // 1. 验证文件数量
  // 2. 验证文件大小
  // 3. 验证文件类型
  // 4. 实时预览
  // 5. 更新文件输入
}
```

---

## 📱 移动端优化

### CSS优化

```css
@media (max-width: 768px) {
  .form-group input[type="file"] { 
    font-size: 16px !important; /* 防止iOS缩放 */
  }
  #image-preview { 
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)) !important; 
  }
}
```

### 触摸优化

- 按钮大小：至少44x44px（iOS推荐）
- 间距：足够的触摸区域
- 预览图片：响应式网格布局

---

## 🚀 使用方法

### 创建交易时上传图片

1. 填写交易信息
2. 点击"选择图片"按钮
3. 从相册选择图片（支持多选）
4. 查看预览，可删除不需要的图片
5. 提交表单

### 编辑交易时上传图片

1. 进入编辑页面
2. 点击"选择图片"按钮
3. 选择新图片
4. 提交保存

### 查看和删除图片

1. 在交易详情页查看已上传的图片
2. 点击图片可放大查看
3. 点击×按钮可删除（需权限）

---

## ⚙️ 配置要求

### 服务器配置

**PHP配置** (`php.ini`):
```ini
upload_max_filesize = 10M
post_max_size = 10M
max_file_uploads = 20
```

**目录权限**:
```bash
# Linux/Mac
mkdir -p public/uploads
chmod 755 public/uploads

# Windows
# 确保 public/uploads 目录存在且有写入权限
```

### 数据库

确保 `attachments` 表已创建（已在 `database/schema.sql` 中定义）

---

## 🐛 常见问题

### 1. 上传失败：文件过大

**原因**: 超过5MB限制或PHP配置限制

**解决**:
- 检查PHP `upload_max_filesize` 和 `post_max_size`
- 压缩图片后再上传

### 2. 移动端无法选择文件

**原因**: 文件输入框被隐藏或样式问题

**解决**:
- 使用按钮触发文件选择
- 确保 `font-size: 16px` 防止iOS缩放

### 3. 图片不显示

**原因**: 文件路径错误或权限问题

**解决**:
- 检查 `public/uploads/` 目录权限
- 检查文件是否成功上传
- 检查URL路径是否正确

### 4. 删除图片失败

**原因**: 权限不足或文件不存在

**解决**:
- 检查用户权限
- 检查文件是否存在
- 查看浏览器控制台错误

---

## 📝 语言包更新

### 新增翻译（中越双语）

- `field.attachments` - 附件（图片）
- `field.attachments_hint` - 附件提示
- `btn.select_images` - 选择图片
- `upload.selected` - 已选择
- `upload.max_files` - 最多文件数
- `upload.file_too_large` - 文件过大
- `upload.invalid_type` - 无效类型
- `upload.delete_confirm` - 删除确认
- `upload.delete_failed` - 删除失败

---

## 🔒 安全考虑

1. **文件类型验证**: 只允许图片格式
2. **文件大小限制**: 防止大文件攻击
3. **唯一文件名**: 防止文件名冲突和覆盖
4. **权限控制**: 只能删除自己的或特定角色
5. **路径安全**: 使用相对路径，防止目录遍历

---

## 📊 文件结构

```
public/
  uploads/              # 上传文件目录
    20241201123456_abc123.jpg
    20241201123457_def456.png
    ...

app/
  models/
    Attachment.php      # 附件模型
  controllers/
    TransactionController.php  # 包含上传处理
  views/
    transactions/
      create.php        # 创建页面（含上传）
      edit.php          # 编辑页面（含上传）
      view.php          # 详情页面（显示图片）
```

---

## ✅ 测试清单

- [x] 桌面端上传功能正常
- [x] 移动端上传功能正常
- [x] 多图片选择正常
- [x] 图片预览正常
- [x] 删除图片功能正常
- [x] 文件大小验证正常
- [x] 文件类型验证正常
- [x] 权限控制正常
- [x] 响应式布局正常

---

**更新日期**: 2024年  
**版本**: v1.3.0

