# 咖啡店ERP系统 UI/UX 界面优化指南

**版本**: 1.0  
**日期**: 2026年1月10日  
**作者**: Manus AI  
**适用范围**: 仅优化界面样式，不修改任何功能逻辑

---

## 目录

1. [设计原则与规范](#1-设计原则与规范)
2. [色彩系统](#2-色彩系统)
3. [字体排版](#3-字体排版)
4. [组件样式优化](#4-组件样式优化)
5. [PC端界面优化](#5-pc端界面优化)
6. [移动端界面优化](#6-移动端界面优化)
7. [各页面具体优化方案](#7-各页面具体优化方案)
8. [CSS代码参考](#8-css代码参考)

---

## 1. 设计原则与规范

### 1.1 核心设计原则

本优化方案遵循以下设计原则，确保界面现代化的同时保持功能完整性：

**一致性原则**：所有页面使用统一的色彩、字体、间距和组件样式，建立品牌识别度。系统中的相同元素（按钮、表单、卡片）在不同页面保持一致的外观和交互方式。

**可读性原则**：确保文字与背景有足够的对比度，重要信息突出显示。数据展示清晰，用户能快速获取关键信息。

**响应式原则**：PC端和移动端采用不同的布局策略，但保持视觉语言统一。移动端优先考虑触控操作的便利性。

**效率原则**：减少用户操作步骤，常用功能易于访问。表单填写流程顺畅，错误提示明确。

### 1.2 设计规范遵循

| 平台 | 设计规范 | 关键要点 |
|------|----------|----------|
| iOS | Human Interface Guidelines | 44pt最小触控区域、系统字体SF Pro、圆角卡片设计 |
| Android | Material Design 3 | 8dp网格系统、动态色彩、涟漪反馈效果 |
| Web | WCAG 2.1 AA | 4.5:1文字对比度、键盘可访问性、焦点状态可见 |

---

## 2. 色彩系统

### 2.1 品牌主色调

建议采用咖啡主题色彩，体现饮品店的品牌特性：

| 用途 | 颜色名称 | HEX值 | RGB值 | 使用场景 |
|------|----------|-------|-------|----------|
| 主色 | 咖啡棕 | #8B4513 | 139, 69, 19 | 品牌标识、主按钮、导航高亮 |
| 主色浅 | 拿铁色 | #D2B48C | 210, 180, 140 | 悬停状态、次要背景 |
| 主色深 | 浓缩咖啡 | #5D3A1A | 93, 58, 26 | 按钮按下状态、标题文字 |
| 辅助色 | 奶油白 | #FFF8F0 | 255, 248, 240 | 页面背景、卡片背景 |

### 2.2 功能色彩

| 用途 | 颜色名称 | HEX值 | 使用场景 |
|------|----------|-------|----------|
| 成功/收入 | 翠绿色 | #27AE60 | 收入金额、成功提示、通过状态 |
| 警告/待审 | 琥珀色 | #F39C12 | 待审批状态、警告提示 |
| 危险/支出 | 珊瑚红 | #E74C3C | 支出金额、删除按钮、错误提示 |
| 信息/链接 | 天蓝色 | #3498DB | 链接文字、信息提示、次要按钮 |
| 中性/禁用 | 灰色 | #95A5A6 | 禁用状态、次要文字、分隔线 |

### 2.3 状态徽章色彩

```css
/* 交易状态 */
.badge-approved { background: #D1ECF1; color: #0C5460; }  /* 已审批 */
.badge-pending { background: #FFF3CD; color: #856404; }   /* 待审批 */
.badge-void { background: #F5C6CB; color: #721C24; }      /* 已作废 */
.badge-rejected { background: #F8D7DA; color: #721C24; }  /* 已拒绝 */

/* 交易类型 */
.badge-income { background: #D4EDDA; color: #155724; }    /* 收入 */
.badge-expense { background: #F8D7DA; color: #721C24; }   /* 支出 */

/* 巡店状态 */
.badge-ok { background: #D4EDDA; color: #155724; }        /* 正常 */
.badge-issue { background: #FFF3CD; color: #856404; }     /* 问题 */
.badge-confirmed { background: #D1ECF1; color: #0C5460; } /* 已确认 */
```

---

## 3. 字体排版

### 3.1 字体家族

```css
/* 系统字体栈 - 优先使用系统原生字体 */
font-family: 
  -apple-system,           /* iOS/macOS */
  BlinkMacSystemFont,      /* macOS Chrome */
  'Segoe UI',              /* Windows */
  'Noto Sans SC',          /* 中文支持 */
  'Roboto',                /* Android */
  'Helvetica Neue',        /* 旧版iOS */
  Arial,                   /* 通用后备 */
  sans-serif;
```

### 3.2 字体大小规范

| 用途 | PC端 | 移动端 | 行高 | 字重 |
|------|------|--------|------|------|
| 页面标题 | 24px | 20px | 1.3 | 700 (Bold) |
| 卡片标题 | 18px | 16px | 1.4 | 600 (Semi-bold) |
| 正文内容 | 14px | 16px | 1.6 | 400 (Regular) |
| 辅助文字 | 12px | 14px | 1.5 | 400 (Regular) |
| 表格内容 | 14px | 12px | 1.5 | 400 (Regular) |
| 按钮文字 | 14px | 16px | 1.2 | 600 (Semi-bold) |
| KPI数字 | 28px | 24px | 1.2 | 700 (Bold) |

### 3.3 文字颜色

| 用途 | HEX值 | 使用场景 |
|------|-------|----------|
| 主要文字 | #1F2937 | 标题、正文、重要信息 |
| 次要文字 | #6B7280 | 说明文字、时间戳、提示 |
| 占位文字 | #9CA3AF | 输入框占位符 |
| 禁用文字 | #D1D5DB | 禁用状态的文字 |
| 链接文字 | #3498DB | 可点击的链接 |

---

## 4. 组件样式优化

### 4.1 按钮组件

**主按钮 (Primary Button)**
```css
.btn-primary {
  background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
  color: #FFFFFF;
  padding: 12px 24px;
  border-radius: 8px;
  border: none;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 2px 4px rgba(139, 69, 19, 0.2);
}

.btn-primary:hover {
  background: linear-gradient(135deg, #A0522D 0%, #8B4513 100%);
  box-shadow: 0 4px 8px rgba(139, 69, 19, 0.3);
  transform: translateY(-1px);
}

.btn-primary:active {
  transform: translateY(0);
  box-shadow: 0 1px 2px rgba(139, 69, 19, 0.2);
}
```

**次要按钮 (Secondary Button)**
```css
.btn-secondary {
  background: #FFFFFF;
  color: #8B4513;
  padding: 12px 24px;
  border-radius: 8px;
  border: 2px solid #8B4513;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-secondary:hover {
  background: #FFF8F0;
  border-color: #A0522D;
}
```

**危险按钮 (Danger Button)**
```css
.btn-danger {
  background: linear-gradient(135deg, #E74C3C 0%, #C0392B 100%);
  color: #FFFFFF;
  padding: 12px 24px;
  border-radius: 8px;
  border: none;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-danger:hover {
  background: linear-gradient(135deg, #C0392B 0%, #E74C3C 100%);
}
```

**成功按钮 (Success Button)**
```css
.btn-success {
  background: linear-gradient(135deg, #27AE60 0%, #229954 100%);
  color: #FFFFFF;
  padding: 12px 24px;
  border-radius: 8px;
  border: none;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-success:hover {
  background: linear-gradient(135deg, #229954 0%, #27AE60 100%);
}
```

### 4.2 卡片组件

**标准卡片**
```css
.card {
  background: #FFFFFF;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(0, 0, 0, 0.05);
  transition: box-shadow 0.2s ease;
}

.card:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
}

.card-title {
  font-size: 18px;
  font-weight: 600;
  color: #1F2937;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.card-title::before {
  content: '';
  width: 4px;
  height: 20px;
  background: #8B4513;
  border-radius: 2px;
}
```

**统计卡片 (KPI Card)**
```css
.kpi-card {
  background: linear-gradient(135deg, #FFFFFF 0%, #FFF8F0 100%);
  border-radius: 16px;
  padding: 24px;
  border: 1px solid rgba(139, 69, 19, 0.1);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.kpi-card .kpi-label {
  font-size: 14px;
  color: #6B7280;
  margin-bottom: 8px;
}

.kpi-card .kpi-value {
  font-size: 28px;
  font-weight: 700;
  margin-bottom: 4px;
}

.kpi-card .kpi-value.income { color: #27AE60; }
.kpi-card .kpi-value.expense { color: #E74C3C; }
.kpi-card .kpi-value.net { color: #8B4513; }

.kpi-card .kpi-trend {
  font-size: 12px;
  color: #6B7280;
}
```

### 4.3 表单组件

**输入框**
```css
.form-input {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #E5E7EB;
  border-radius: 8px;
  font-size: 14px;
  color: #1F2937;
  background: #FFFFFF;
  transition: all 0.2s ease;
}

.form-input:focus {
  outline: none;
  border-color: #8B4513;
  box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
}

.form-input::placeholder {
  color: #9CA3AF;
}

.form-input:disabled {
  background: #F3F4F6;
  color: #9CA3AF;
  cursor: not-allowed;
}
```

**下拉选择框**
```css
.form-select {
  width: 100%;
  padding: 12px 40px 12px 16px;
  border: 2px solid #E5E7EB;
  border-radius: 8px;
  font-size: 14px;
  color: #1F2937;
  background: #FFFFFF url('data:image/svg+xml,...') no-repeat right 12px center;
  background-size: 16px;
  appearance: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.form-select:focus {
  outline: none;
  border-color: #8B4513;
  box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
}
```

**表单标签**
```css
.form-label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  font-size: 14px;
  color: #374151;
}

.form-label.required::after {
  content: ' *';
  color: #E74C3C;
}
```

**表单组**
```css
.form-group {
  margin-bottom: 20px;
}

.form-hint {
  margin-top: 6px;
  font-size: 12px;
  color: #6B7280;
}

.form-error {
  margin-top: 6px;
  font-size: 12px;
  color: #E74C3C;
}
```

### 4.4 表格组件

```css
.data-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  background: #FFFFFF;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.data-table thead {
  background: linear-gradient(135deg, #F8F9FA 0%, #F3F4F6 100%);
}

.data-table th {
  padding: 14px 16px;
  text-align: left;
  font-weight: 600;
  font-size: 13px;
  color: #374151;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 2px solid #E5E7EB;
}

.data-table td {
  padding: 14px 16px;
  font-size: 14px;
  color: #1F2937;
  border-bottom: 1px solid #F3F4F6;
  vertical-align: middle;
}

.data-table tbody tr {
  transition: background 0.15s ease;
}

.data-table tbody tr:hover {
  background: #FFF8F0;
}

.data-table tbody tr:last-child td {
  border-bottom: none;
}

/* 响应式表格 */
.table-responsive {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  margin: 0 -20px;
  padding: 0 20px;
}
```

### 4.5 徽章组件

```css
.badge {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
}

/* 带图标的徽章 */
.badge-icon {
  width: 14px;
  height: 14px;
  margin-right: 4px;
}
```

### 4.6 提示框组件

```css
.alert {
  padding: 14px 18px;
  border-radius: 10px;
  margin-bottom: 16px;
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.alert-icon {
  flex-shrink: 0;
  width: 20px;
  height: 20px;
}

.alert-success {
  background: #D4EDDA;
  border: 1px solid #C3E6CB;
  color: #155724;
}

.alert-error {
  background: #F8D7DA;
  border: 1px solid #F5C6CB;
  color: #721C24;
}

.alert-warning {
  background: #FFF3CD;
  border: 1px solid #FFEEBA;
  color: #856404;
}

.alert-info {
  background: #D1ECF1;
  border: 1px solid #BEE5EB;
  color: #0C5460;
}
```

---

## 5. PC端界面优化

### 5.1 侧边栏导航优化

**当前问题**：
- 导航栏背景色较暗，与咖啡店品牌不符
- 导航项间距较小，点击区域不够明显
- 缺少图标辅助识别

**优化方案**：

```css
/* 侧边栏容器 */
.sidebar {
  width: 260px;
  background: linear-gradient(180deg, #5D3A1A 0%, #3D2512 100%);
  color: #FFF8F0;
  padding: 24px 16px;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  position: fixed;
  left: 0;
  top: 0;
  z-index: 100;
}

/* 品牌标识 */
.sidebar .brand {
  font-size: 20px;
  font-weight: 700;
  color: #FFFFFF;
  margin-bottom: 32px;
  padding: 0 12px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.sidebar .brand-icon {
  width: 36px;
  height: 36px;
  background: #8B4513;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
}

/* 导航分组 */
.nav-group {
  margin-bottom: 24px;
}

.nav-group-title {
  font-size: 11px;
  font-weight: 600;
  color: rgba(255, 248, 240, 0.5);
  text-transform: uppercase;
  letter-spacing: 1px;
  padding: 0 12px;
  margin-bottom: 8px;
}

/* 导航项 */
.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-radius: 10px;
  color: rgba(255, 248, 240, 0.8);
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s ease;
  margin-bottom: 4px;
}

.nav-item:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #FFFFFF;
}

.nav-item.active {
  background: #8B4513;
  color: #FFFFFF;
  box-shadow: 0 2px 8px rgba(139, 69, 19, 0.3);
}

.nav-item-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

/* 用户信息区 */
.sidebar-footer {
  margin-top: auto;
  padding-top: 24px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.05);
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #8B4513;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  color: #FFFFFF;
}

.user-name {
  font-weight: 500;
  color: #FFFFFF;
}

.user-role {
  font-size: 12px;
  color: rgba(255, 248, 240, 0.6);
}
```

### 5.2 顶部栏优化

```css
.topbar {
  background: #FFFFFF;
  padding: 16px 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid #E5E7EB;
  position: sticky;
  top: 0;
  z-index: 50;
  margin-left: 260px; /* 侧边栏宽度 */
}

.topbar-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.page-title {
  font-size: 20px;
  font-weight: 600;
  color: #1F2937;
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #6B7280;
}

.breadcrumb a {
  color: #8B4513;
  text-decoration: none;
}

.breadcrumb a:hover {
  text-decoration: underline;
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

.lang-switch {
  display: flex;
  gap: 8px;
}

.lang-btn {
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 500;
  border: 1px solid #E5E7EB;
  background: #FFFFFF;
  color: #6B7280;
  cursor: pointer;
  transition: all 0.2s ease;
}

.lang-btn.active,
.lang-btn:hover {
  background: #8B4513;
  color: #FFFFFF;
  border-color: #8B4513;
}
```

### 5.3 主内容区优化

```css
.main-content {
  margin-left: 260px; /* 侧边栏宽度 */
  padding: 24px;
  background: #F8F9FA;
  min-height: 100vh;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
}

/* 页面标题区 */
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
}

.page-header h1 {
  font-size: 24px;
  font-weight: 700;
  color: #1F2937;
}

.page-actions {
  display: flex;
  gap: 12px;
}
```

---

## 6. 移动端界面优化

### 6.1 移动端设计原则

移动端界面优化需遵循iOS Human Interface Guidelines和Material Design规范，确保未来可顺利上架App Store和Google Play。

**关键设计要点**：

| 要点 | iOS规范 | Android规范 | 实施建议 |
|------|---------|-------------|----------|
| 最小触控区域 | 44×44 pt | 48×48 dp | 按钮和可点击元素至少48px |
| 安全区域 | 顶部刘海、底部Home指示器 | 状态栏、导航栏 | 使用safe-area-inset |
| 导航模式 | 底部Tab栏 | 底部导航栏 | 采用固定底部导航 |
| 手势操作 | 边缘滑动返回 | 返回按钮 | 保留返回按钮同时支持手势 |
| 字体大小 | 最小11pt | 最小12sp | 正文至少16px |

### 6.2 移动端头部优化

```css
.h5-header {
  background: #FFFFFF;
  padding: 12px 16px;
  display: flex;
  align-items: center;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  /* iOS安全区域适配 */
  padding-top: calc(12px + env(safe-area-inset-top));
}

.h5-back-btn {
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-left: -12px;
  color: #8B4513;
  font-size: 24px;
  text-decoration: none;
  border-radius: 50%;
  transition: background 0.2s ease;
}

.h5-back-btn:active {
  background: rgba(139, 69, 19, 0.1);
}

.h5-title {
  flex: 1;
  font-size: 18px;
  font-weight: 600;
  color: #1F2937;
  text-align: center;
  margin-right: 44px; /* 平衡返回按钮的空间 */
}

.h5-header-action {
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #8B4513;
}
```

### 6.3 移动端底部导航

```css
.h5-bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: #FFFFFF;
  display: flex;
  justify-content: space-around;
  padding: 8px 0;
  /* iOS安全区域适配 */
  padding-bottom: calc(8px + env(safe-area-inset-bottom));
  border-top: 1px solid #E5E7EB;
  box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.08);
  z-index: 100;
}

.h5-nav-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  color: #6B7280;
  font-size: 11px;
  font-weight: 500;
  padding: 4px 16px;
  min-width: 64px;
  transition: color 0.2s ease;
}

.h5-nav-item.active {
  color: #8B4513;
}

.h5-nav-icon {
  font-size: 24px;
  margin-bottom: 4px;
  transition: transform 0.2s ease;
}

.h5-nav-item.active .h5-nav-icon {
  transform: scale(1.1);
}

.h5-nav-label {
  white-space: nowrap;
}
```

### 6.4 移动端卡片样式

```css
.h5-card {
  background: #FFFFFF;
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 16px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}

.h5-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}

.h5-card-title {
  font-size: 16px;
  font-weight: 600;
  color: #1F2937;
  display: flex;
  align-items: center;
  gap: 8px;
}

.h5-card-action {
  color: #8B4513;
  font-size: 14px;
  text-decoration: none;
}

/* 可点击卡片 */
.h5-card-clickable {
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.h5-card-clickable:active {
  transform: scale(0.98);
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
}
```

### 6.5 移动端表单优化

```css
.h5-form-group {
  margin-bottom: 20px;
}

.h5-form-label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  font-size: 15px;
  color: #374151;
}

.h5-form-input,
.h5-form-select,
.h5-form-textarea {
  width: 100%;
  padding: 14px 16px;
  border: 2px solid #E5E7EB;
  border-radius: 12px;
  font-size: 16px; /* 防止iOS自动缩放 */
  color: #1F2937;
  background: #FFFFFF;
  transition: all 0.2s ease;
  -webkit-appearance: none;
}

.h5-form-input:focus,
.h5-form-select:focus,
.h5-form-textarea:focus {
  outline: none;
  border-color: #8B4513;
  box-shadow: 0 0 0 4px rgba(139, 69, 19, 0.1);
}

.h5-form-textarea {
  min-height: 120px;
  resize: vertical;
}

.h5-form-hint {
  margin-top: 8px;
  font-size: 13px;
  color: #6B7280;
}

/* 移动端按钮 */
.h5-btn {
  display: block;
  width: 100%;
  padding: 16px;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  text-align: center;
  text-decoration: none;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.h5-btn-primary {
  background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
  color: #FFFFFF;
  box-shadow: 0 4px 12px rgba(139, 69, 19, 0.3);
}

.h5-btn-primary:active {
  transform: scale(0.98);
  box-shadow: 0 2px 6px rgba(139, 69, 19, 0.2);
}

.h5-btn-success {
  background: linear-gradient(135deg, #27AE60 0%, #229954 100%);
  color: #FFFFFF;
}

.h5-btn-danger {
  background: linear-gradient(135deg, #E74C3C 0%, #C0392B 100%);
  color: #FFFFFF;
}

/* 固定底部按钮 */
.h5-fixed-bottom {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 16px;
  padding-bottom: calc(16px + env(safe-area-inset-bottom));
  background: #FFFFFF;
  box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.08);
  z-index: 90;
}
```

### 6.6 移动端统计卡片

```css
/* 顶部统计区域 */
.h5-stats-header {
  background: linear-gradient(135deg, #8B4513 0%, #5D3A1A 100%);
  color: #FFFFFF;
  padding: 24px 20px;
  margin: -16px -16px 20px -16px;
  border-radius: 0 0 24px 24px;
}

.h5-stats-greeting {
  font-size: 14px;
  opacity: 0.9;
  margin-bottom: 4px;
}

.h5-stats-date {
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 20px;
}

.h5-stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}

.h5-stat-item {
  text-align: center;
}

.h5-stat-label {
  font-size: 12px;
  opacity: 0.8;
  margin-bottom: 4px;
}

.h5-stat-value {
  font-size: 18px;
  font-weight: 700;
}

.h5-stat-value.income { color: #90EE90; }
.h5-stat-value.expense { color: #FFB6C1; }
.h5-stat-value.net { color: #FFFFFF; }
```

---

## 7. 各页面具体优化方案

### 7.1 登录页面 (auth/login.php)

**优化要点**：
- 添加品牌Logo和咖啡店背景图
- 表单居中显示，卡片式设计
- 输入框增加图标前缀
- 登录按钮使用品牌主色

```css
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #FFF8F0 0%, #F5E6D3 100%);
  padding: 20px;
}

.login-card {
  width: 100%;
  max-width: 400px;
  background: #FFFFFF;
  border-radius: 20px;
  padding: 40px;
  box-shadow: 0 10px 40px rgba(139, 69, 19, 0.15);
}

.login-logo {
  text-align: center;
  margin-bottom: 32px;
}

.login-logo img {
  width: 80px;
  height: 80px;
}

.login-title {
  font-size: 24px;
  font-weight: 700;
  color: #1F2937;
  text-align: center;
  margin-bottom: 8px;
}

.login-subtitle {
  font-size: 14px;
  color: #6B7280;
  text-align: center;
  margin-bottom: 32px;
}
```

### 7.2 仪表盘页面 (reports/dashboard.php)

**优化要点**：
- KPI卡片使用渐变背景和图标
- 7日趋势使用图表替代表格
- 最近流水使用时间线样式

**KPI卡片布局**：
```html
<div class="kpi-grid">
  <div class="kpi-card">
    <div class="kpi-icon income">📈</div>
    <div class="kpi-content">
      <div class="kpi-label">今日收入</div>
      <div class="kpi-value income">+1,234,000 ₫</div>
      <div class="kpi-trend">↑ 12% 较昨日</div>
    </div>
  </div>
  <!-- 更多KPI卡片... -->
</div>
```

```css
.kpi-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  margin-bottom: 24px;
}

.kpi-card {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  padding: 24px;
}

.kpi-icon {
  width: 56px;
  height: 56px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  flex-shrink: 0;
}

.kpi-icon.income {
  background: linear-gradient(135deg, #D4EDDA 0%, #C3E6CB 100%);
}

.kpi-icon.expense {
  background: linear-gradient(135deg, #F8D7DA 0%, #F5C6CB 100%);
}

.kpi-icon.net {
  background: linear-gradient(135deg, #FFF3CD 0%, #FFEEBA 100%);
}
```

### 7.3 交易列表页面 (transactions/list.php)

**优化要点**：
- 筛选器使用折叠面板，默认收起
- 表格增加斑马纹和悬停效果
- 金额列右对齐，使用等宽字体
- 操作按钮使用图标

```css
/* 筛选器面板 */
.filter-panel {
  background: #FFFFFF;
  border-radius: 12px;
  margin-bottom: 20px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.filter-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  cursor: pointer;
  border-bottom: 1px solid #E5E7EB;
}

.filter-header h3 {
  font-size: 16px;
  font-weight: 600;
  color: #1F2937;
  display: flex;
  align-items: center;
  gap: 8px;
}

.filter-toggle {
  width: 24px;
  height: 24px;
  transition: transform 0.2s ease;
}

.filter-panel.collapsed .filter-toggle {
  transform: rotate(-90deg);
}

.filter-body {
  padding: 20px;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
}

.filter-panel.collapsed .filter-body {
  display: none;
}

/* 金额列样式 */
.amount-cell {
  font-family: 'SF Mono', 'Roboto Mono', monospace;
  font-weight: 600;
  text-align: right;
}

.amount-cell.income {
  color: #27AE60;
}

.amount-cell.expense {
  color: #E74C3C;
}
```

### 7.4 交易创建页面 (transactions/create.php)

**优化要点**：
- 分步骤表单，清晰的进度指示
- 金额输入使用大字体
- 分类和支付方式使用图标网格选择

```css
/* 金额输入 */
.amount-input-wrapper {
  position: relative;
  margin-bottom: 24px;
}

.amount-input {
  width: 100%;
  padding: 20px 60px 20px 40px;
  font-size: 32px;
  font-weight: 700;
  font-family: 'SF Mono', 'Roboto Mono', monospace;
  border: 2px solid #E5E7EB;
  border-radius: 16px;
  text-align: right;
}

.amount-currency {
  position: absolute;
  right: 20px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 18px;
  color: #6B7280;
}

/* 分类选择网格 */
.category-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
  gap: 12px;
}

.category-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 16px 12px;
  border: 2px solid #E5E7EB;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.category-item:hover {
  border-color: #8B4513;
  background: #FFF8F0;
}

.category-item.selected {
  border-color: #8B4513;
  background: #8B4513;
  color: #FFFFFF;
}

.category-icon {
  font-size: 24px;
  margin-bottom: 8px;
}

.category-name {
  font-size: 12px;
  font-weight: 500;
  text-align: center;
}
```

### 7.5 巡店检查页面 (inspections/create.php)

**优化要点**：
- 楼层和区域使用大按钮网格选择
- 状态选择使用滑动开关
- 照片上传区域更醒目
- 进度指示器显示今日巡店完成情况

```css
/* 楼层选择 */
.floor-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
  margin-bottom: 20px;
}

.floor-btn {
  padding: 16px;
  border: 2px solid #E5E7EB;
  border-radius: 12px;
  background: #FFFFFF;
  font-size: 18px;
  font-weight: 600;
  color: #1F2937;
  cursor: pointer;
  transition: all 0.2s ease;
}

.floor-btn:hover {
  border-color: #8B4513;
  background: #FFF8F0;
}

.floor-btn.selected {
  border-color: #8B4513;
  background: #8B4513;
  color: #FFFFFF;
}

/* 状态开关 */
.status-switch {
  display: flex;
  background: #F3F4F6;
  border-radius: 12px;
  padding: 4px;
}

.status-option {
  flex: 1;
  padding: 12px 20px;
  border-radius: 10px;
  font-weight: 600;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.status-option.ok.selected {
  background: #27AE60;
  color: #FFFFFF;
}

.status-option.issue.selected {
  background: #F39C12;
  color: #FFFFFF;
}

/* 照片上传区 */
.photo-upload {
  border: 3px dashed #D1D5DB;
  border-radius: 16px;
  padding: 32px;
  text-align: center;
  background: #F9FAFB;
  transition: all 0.2s ease;
}

.photo-upload:hover,
.photo-upload.dragover {
  border-color: #8B4513;
  background: #FFF8F0;
}

.photo-upload-icon {
  font-size: 48px;
  margin-bottom: 12px;
}

.photo-upload-text {
  font-size: 16px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 4px;
}

.photo-upload-hint {
  font-size: 14px;
  color: #6B7280;
}

/* 照片预览 */
.photo-preview-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
  gap: 12px;
  margin-top: 16px;
}

.photo-preview-item {
  position: relative;
  aspect-ratio: 1;
  border-radius: 12px;
  overflow: hidden;
}

.photo-preview-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.photo-preview-remove {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: rgba(231, 76, 60, 0.9);
  color: #FFFFFF;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}
```

### 7.6 员工管理页面 (employees/list.php)

**优化要点**：
- 员工列表使用卡片网格而非表格
- 显示员工头像和基本信息
- 状态标签更醒目

```css
.employee-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}

.employee-card {
  background: #FFFFFF;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  transition: all 0.2s ease;
}

.employee-card:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.employee-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 16px;
}

.employee-avatar {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  font-weight: 600;
  color: #FFFFFF;
}

.employee-info h3 {
  font-size: 16px;
  font-weight: 600;
  color: #1F2937;
  margin-bottom: 4px;
}

.employee-position {
  font-size: 14px;
  color: #6B7280;
}

.employee-details {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  padding-top: 16px;
  border-top: 1px solid #F3F4F6;
}

.employee-detail-item {
  font-size: 13px;
}

.employee-detail-label {
  color: #6B7280;
  margin-bottom: 2px;
}

.employee-detail-value {
  color: #1F2937;
  font-weight: 500;
}
```

### 7.7 排班管理页面 (shifts/schedule.php)

**优化要点**：
- 使用日历视图展示排班
- 不同班次使用不同颜色
- 支持拖拽调整排班

```css
.schedule-calendar {
  background: #FFFFFF;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.calendar-header {
  display: grid;
  grid-template-columns: 100px repeat(7, 1fr);
  background: #F8F9FA;
  border-bottom: 1px solid #E5E7EB;
}

.calendar-header-cell {
  padding: 16px;
  text-align: center;
  font-weight: 600;
  font-size: 14px;
  color: #374151;
}

.calendar-body {
  display: grid;
  grid-template-columns: 100px repeat(7, 1fr);
}

.calendar-time-cell {
  padding: 12px;
  font-size: 13px;
  color: #6B7280;
  border-right: 1px solid #F3F4F6;
  border-bottom: 1px solid #F3F4F6;
}

.calendar-cell {
  min-height: 80px;
  padding: 8px;
  border-right: 1px solid #F3F4F6;
  border-bottom: 1px solid #F3F4F6;
}

.shift-tag {
  padding: 6px 10px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 500;
  margin-bottom: 4px;
}

.shift-tag.morning {
  background: #D4EDDA;
  color: #155724;
}

.shift-tag.afternoon {
  background: #D1ECF1;
  color: #0C5460;
}

.shift-tag.evening {
  background: #E2D4F0;
  color: #6F42C1;
}
```

### 7.8 现金日结页面 (cash_closings/create.php)

**优化要点**：
- 金额对比使用可视化进度条
- 差额突出显示
- 计算过程透明展示

```css
.cash-comparison {
  background: #FFFFFF;
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 20px;
}

.cash-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 0;
  border-bottom: 1px solid #F3F4F6;
}

.cash-row:last-child {
  border-bottom: none;
}

.cash-label {
  font-size: 15px;
  color: #6B7280;
}

.cash-value {
  font-size: 20px;
  font-weight: 700;
  font-family: 'SF Mono', 'Roboto Mono', monospace;
}

.cash-value.theoretical {
  color: #3498DB;
}

.cash-value.actual {
  color: #1F2937;
}

.cash-difference {
  padding: 20px;
  border-radius: 12px;
  text-align: center;
  margin-top: 16px;
}

.cash-difference.positive {
  background: #D4EDDA;
}

.cash-difference.negative {
  background: #F8D7DA;
}

.cash-difference.zero {
  background: #D1ECF1;
}

.difference-label {
  font-size: 14px;
  margin-bottom: 8px;
}

.difference-value {
  font-size: 28px;
  font-weight: 700;
  font-family: 'SF Mono', 'Roboto Mono', monospace;
}

.difference-value.positive {
  color: #27AE60;
}

.difference-value.negative {
  color: #E74C3C;
}
```

---

## 8. CSS代码参考

### 8.1 完整的PC端样式文件

将以下CSS代码保存为 `assets/css/pc-style.css`，在 `layout/header.php` 中引入：

```css
/* ========================================
   咖啡店ERP系统 - PC端样式
   版本: 1.0
   ======================================== */

/* 基础重置 */
*, *::before, *::after {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html {
  font-size: 16px;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Noto Sans SC', Roboto, 'Helvetica Neue', Arial, sans-serif;
  background: #F8F9FA;
  color: #1F2937;
  line-height: 1.6;
}

/* 布局 */
.layout {
  display: flex;
  min-height: 100vh;
}

/* 侧边栏 - 参考 5.1 节 */
/* 顶部栏 - 参考 5.2 节 */
/* 主内容区 - 参考 5.3 节 */

/* 组件样式 - 参考第4节 */

/* 页面特定样式 - 参考第7节 */
```

### 8.2 完整的移动端样式文件

将以下CSS代码保存为 `assets/css/h5-style.css`，在 `layout/h5_header.php` 中引入：

```css
/* ========================================
   咖啡店ERP系统 - 移动端样式
   版本: 1.0
   ======================================== */

/* 基础重置 */
*, *::before, *::after {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html {
  font-size: 16px;
  -webkit-font-smoothing: antialiased;
  -webkit-tap-highlight-color: transparent;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Noto Sans SC', Roboto, 'Helvetica Neue', Arial, sans-serif;
  background: #F8F9FA;
  color: #1F2937;
  line-height: 1.6;
  padding-bottom: calc(60px + env(safe-area-inset-bottom));
  min-height: 100vh;
  overflow-x: hidden;
}

/* 移动端头部 - 参考 6.2 节 */
/* 移动端底部导航 - 参考 6.3 节 */
/* 移动端卡片 - 参考 6.4 节 */
/* 移动端表单 - 参考 6.5 节 */
/* 移动端统计卡片 - 参考 6.6 节 */
```

---

## 附录：图标建议

建议使用以下图标库之一：

| 图标库 | CDN引入方式 | 特点 |
|--------|-------------|------|
| Lucide Icons | `<script src="https://unpkg.com/lucide@latest"></script>` | 轻量、现代、与Tailwind配合好 |
| Heroicons | `<script src="https://unpkg.com/heroicons@latest"></script>` | 由Tailwind团队制作 |
| Font Awesome | `<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">` | 图标最全面 |

**推荐的功能图标映射**：

| 功能 | 建议图标 | 备选 |
|------|----------|------|
| 仪表盘 | 📊 / LayoutDashboard | Home |
| 新增交易 | ➕ / PlusCircle | Plus |
| 交易列表 | 📋 / List | FileText |
| 收入 | 📈 / TrendingUp | ArrowUpCircle |
| 支出 | 📉 / TrendingDown | ArrowDownCircle |
| 巡店检查 | 🔍 / Search | ClipboardCheck |
| 员工管理 | 👥 / Users | UserCircle |
| 排班管理 | 📅 / Calendar | CalendarDays |
| 任务管理 | ✅ / CheckSquare | ListTodo |
| 库存管理 | 📦 / Package | Boxes |
| 资产管理 | 🏷️ / Tag | Archive |
| 现金日结 | 💰 / Wallet | Banknote |
| 设置 | ⚙️ / Settings | Cog |
| 登出 | 🚪 / LogOut | DoorOpen |

---

**文档结束**

如需进一步的设计支持或有任何问题，请随时联系。
