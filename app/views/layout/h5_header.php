<!doctype html>
<html lang="<?= I18n::current() ?>">
<head>
<meta charset="utf-8">
<title><?= $title ?? __('app.title') ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<meta name="description" content="<?= __('app.title') ?>">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { 
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; 
    background: #f5f5f5; 
    color: #333; 
    line-height: 1.6;
    padding-bottom: 60px; /* 为底部导航留空间 */
  }
  
  /* H5 Header */
  .h5-header {
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  }
  .h5-header .back-btn {
    font-size: 20px;
    color: #3498db;
    text-decoration: none;
    margin-right: 12px;
    padding: 4px 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
  }
  .h5-header .title {
    flex: 1;
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
  }
  
  /* 内容区 */
  .h5-content {
    padding: 16px;
    min-height: calc(100vh - 120px);
  }
  
  /* 卡片样式 */
  .h5-card {
    background: white;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  }
  .h5-card-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 12px;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  
  /* 状态颜色 */
  .status-red { color: #e74c3c; }
  .status-yellow { color: #f39c12; }
  .status-orange { color: #ff9800; }
  .status-green { color: #27ae60; }
  
  /* 按钮 */
  .h5-btn {
    display: block;
    width: 100%;
    padding: 14px;
    background: #3498db;
    color: white;
    text-align: center;
    text-decoration: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    margin-top: 12px;
  }
  .h5-btn:hover { background: #2980b9; }
  .h5-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
  }
  .h5-btn-success { background: #27ae60; }
  .h5-btn-success:hover { background: #229954; }
  
  /* 表单 */
  .h5-form-group {
    margin-bottom: 16px;
  }
  .h5-form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #374151;
  }
  .h5-form-group input,
  .h5-form-group textarea,
  .h5-form-group select {
    width: 100%;
    padding: 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 16px;
    background: #fff;
  }
  .h5-form-group textarea {
    min-height: 100px;
    resize: vertical;
  }
  .h5-form-group small {
    display: block;
    margin-top: 4px;
    font-size: 12px;
    color: #6b7280;
  }
  
  /* 底部导航 */
  .h5-bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-around;
    padding: 8px 0;
    z-index: 100;
    box-shadow: 0 -2px 8px rgba(0,0,0,0.1);
  }
  .h5-nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: #6b7280;
    font-size: 12px;
    padding: 4px 12px;
    flex: 1;
  }
  .h5-nav-item.active {
    color: #3498db;
  }
  .h5-nav-item .icon {
    font-size: 24px;
    margin-bottom: 4px;
  }
  
  /* 状态卡片 */
  .status-card {
    background: white;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .status-card:active {
    transform: scale(0.98);
    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
  }
  .status-card-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
  }
  .status-card-title {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
  }
  .status-card-body {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 8px;
  }
  .status-card-footer {
    font-size: 14px;
    color: #6b7280;
  }
  
  /* 顶部信息区 */
  .top-info {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 16px;
    margin: -16px -16px 16px -16px;
    border-radius: 0 0 16px 16px;
  }
  .top-info .greeting {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 8px;
  }
  .top-info .date {
    font-size: 14px;
    opacity: 0.9;
  }
  
  /* 拍照区 */
  .photo-upload-area {
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    padding: 24px;
    text-align: center;
    background: #f9fafb;
    margin-bottom: 12px;
  }
  .photo-upload-area.has-photos {
    border-style: solid;
    border-color: #3498db;
    background: #fff;
  }
  .photo-preview {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 12px;
  }
  .photo-item {
    position: relative;
    width: 80px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    background: #f3f4f6;
  }
  .photo-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .photo-item .remove {
    position: absolute;
    top: 4px;
    right: 4px;
    background: rgba(0,0,0,0.6);
    color: white;
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  /* 问题类型选择 */
  .issue-type-group {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  .issue-type-item {
    display: flex;
    align-items: center;
    padding: 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
  }
  .issue-type-item:hover {
    border-color: #3498db;
    background: #f0f7ff;
  }
  .issue-type-item input[type="radio"] {
    width: 20px;
    height: 20px;
    margin-right: 12px;
  }
  .issue-type-item.selected {
    border-color: #3498db;
    background: #f0f7ff;
  }
  
  /* 金额显示 */
  .amount-display {
    background: #f9fafb;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 16px;
  }
  .amount-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #e5e7eb;
  }
  .amount-row:last-child {
    border-bottom: none;
    font-weight: bold;
    font-size: 18px;
    margin-top: 8px;
    padding-top: 12px;
  }
  .amount-label {
    color: #6b7280;
  }
  .amount-value {
    font-weight: 600;
    color: #1f2937;
  }
  .amount-positive { color: #27ae60; }
  .amount-negative { color: #e74c3c; }
  
  /* 固定底部按钮 */
  .fixed-bottom-btn {
    position: fixed;
    bottom: 70px;
    left: 16px;
    right: 16px;
    z-index: 99;
  }
  
  /* 提示信息 */
  .h5-hint {
    font-size: 12px;
    color: #6b7280;
    margin-top: 4px;
  }
  .h5-hint.required {
    color: #e74c3c;
    font-weight: 500;
  }
  
  /* 响应式 */
  @media (min-width: 768px) {
    .h5-content {
      max-width: 600px;
      margin: 0 auto;
    }
  }
</style>
</head>
<body>
<?php if (Auth::check()): ?>
<div class="h5-header">
  <?php if (isset($show_back) && $show_back): ?>
  <a href="javascript:history.back()" class="back-btn">←</a>
  <?php endif; ?>
  <div class="title"><?= $title ?? __('app.title') ?></div>
</div>
<div class="h5-content">
<?php else: ?>
<div class="h5-content" style="padding-top: 0;">
<?php endif; ?>



