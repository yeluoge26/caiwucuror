<!doctype html>
<html lang="<?= I18n::current() ?>">
<head>
<meta charset="utf-8">
<title><?= $title ?? __('app.title') ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="<?= __('app.title') ?>">
<meta name="keywords" content="Teah Space, tài chính, 财务, 管理系统, finance">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; 
         background: #f5f5f5; color: #333; line-height: 1.6; }
  .layout { display: flex; min-height: 100vh; }
  .sidebar { width: 240px; background: #1f2a44; color: #e8eef7; padding: 20px 16px; }
  .sidebar .brand { font-weight: 700; font-size: 18px; margin-bottom: 20px; color: #fff; letter-spacing: 0.2px; }
  .nav-vertical { display: flex; flex-direction: column; gap: 8px; }
  .nav-vertical a { color: #e8eef7; text-decoration: none; padding: 10px 12px; border-radius: 6px; transition: background 0.2s, color 0.2s; }
  .nav-vertical a:hover { background: rgba(255,255,255,0.08); }
  .nav-vertical a.active { background: #2f405f; color: #fff; box-shadow: inset 0 0 0 1px rgba(255,255,255,0.08); }
  .sidebar .hint { margin-top: 20px; font-size: 12px; color: #9fb2d6; }
  .topbar { background: #fff; padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; 
            border-bottom: 1px solid #e5e7eb; position: sticky; top: 0; z-index: 10; }
  .topbar .lang-switch a { color: #3498db; text-decoration: none; margin-right: 10px; font-weight: 600; }
  .topbar .lang-switch a:last-child { margin-right: 0; }
  .topbar .user-box { display: flex; align-items: center; gap: 10px; }
  .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
  .card { background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
  .btn { display: inline-block; padding: 10px 20px; background: #3498db; color: white; 
         text-decoration: none; border-radius: 4px; border: none; cursor: pointer; font-size: 14px; }
  .btn:hover { background: #2980b9; }
  .btn-danger { background: #e74c3c; }
  .btn-danger:hover { background: #c0392b; }
  .btn-success { background: #27ae60; }
  .btn-success:hover { background: #229954; }
  .form-group { margin-bottom: 16px; }
  .form-group label { display: block; margin-bottom: 6px; font-weight: 500; }
  .form-group input, .form-group select, .form-group textarea { 
    width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
  .form-group textarea { min-height: 80px; resize: vertical; }
  table { width: 100%; border-collapse: collapse; background: white; }
  table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
  table th { background: #f8f9fa; font-weight: 600; }
  table tr:hover { background: #f8f9fa; }
  .kpi { font-size: 24px; font-weight: bold; margin: 8px 0; }
  .kpi.income { color: #27ae60; }
  .kpi.expense { color: #e74c3c; }
  .kpi.net { color: #3498db; }
  .alert { padding: 12px; border-radius: 4px; margin-bottom: 16px; }
  .alert-error { background: #fee; color: #c33; border: 1px solid #fcc; }
  .alert-success { background: #efe; color: #3c3; border: 1px solid #cfc; }
  .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
  .badge-income { background: #d4edda; color: #155724; }
  .badge-expense { background: #f8d7da; color: #721c24; }
  .badge-approved { background: #d1ecf1; color: #0c5460; }
  .badge-pending { background: #fff3cd; color: #856404; }
  .badge-void { background: #f5c6cb; color: #721c24; }
  @media (max-width: 1024px) {
    .layout { flex-direction: column; }
    .sidebar { width: 100%; display: flex; align-items: center; justify-content: space-between; }
    .nav-vertical { flex-direction: row; flex-wrap: nowrap; overflow-x: auto; }
    .nav-vertical a { padding: 8px 10px; white-space: nowrap; }
    .container { padding: 12px; }
  }
  @media (max-width: 768px) {
    table { font-size: 12px; }
    table th, table td { padding: 8px; }
    .topbar { flex-direction: column; align-items: flex-start; gap: 10px; }
    .sidebar { flex-direction: column; align-items: flex-start; gap: 10px; }
    .brand { margin-bottom: 8px; }
  }
  .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
</style>
</head>
<body>
<?php if (Auth::check()): ?>
<div class="layout">
  <aside class="sidebar">
    <div class="brand"><?= __('app.name') ?></div>
    <nav class="nav-vertical">
      <a href="/index.php?r=reports/dashboard" class="<?= ($_GET['r'] ?? '') === 'reports/dashboard' ? 'active' : '' ?>">
        <?= __('nav.dashboard') ?>
      </a>
      <a href="/index.php?r=transactions/create" class="<?= ($_GET['r'] ?? '') === 'transactions/create' ? 'active' : '' ?>">
        <?= __('nav.add') ?>
      </a>
      <a href="/index.php?r=transactions/list" class="<?= ($_GET['r'] ?? '') === 'transactions/list' ? 'active' : '' ?>">
        <?= __('nav.list') ?>
      </a>
      <?php if (in_array(Auth::user()['role_key'], ['owner', 'manager', 'accountant'])): ?>
      <a href="/index.php?r=inspections/create" class="<?= ($_GET['r'] ?? '') === 'inspections/create' ? 'active' : '' ?>">
        <?= __('nav.inspections_create') ?>
      </a>
      <a href="/index.php?r=inspections/list" class="<?= ($_GET['r'] ?? '') === 'inspections/list' ? 'active' : '' ?>">
        <?= __('nav.inspections') ?>
      </a>
      <a href="/index.php?r=materials/create" class="<?= ($_GET['r'] ?? '') === 'materials/create' ? 'active' : '' ?>">
        <?= __('nav.materials_create') ?>
      </a>
      <a href="/index.php?r=materials/list" class="<?= ($_GET['r'] ?? '') === 'materials/list' ? 'active' : '' ?>">
        <?= __('nav.materials_list') ?>
      </a>
      <a href="/index.php?r=assets/create" class="<?= ($_GET['r'] ?? '') === 'assets/create' ? 'active' : '' ?>">
        <?= __('nav.assets_create') ?>
      </a>
      <a href="/index.php?r=assets/list" class="<?= ($_GET['r'] ?? '') === 'assets/list' ? 'active' : '' ?>">
        <?= __('nav.assets_list') ?>
      </a>
      <a href="/index.php?r=drinks/recipes" class="<?= ($_GET['r'] ?? '') === 'drinks/recipes' ? 'active' : '' ?>">
        <?= __('nav.recipes') ?>
      </a>
      <a href="/index.php?r=drinks/consume" class="<?= ($_GET['r'] ?? '') === 'drinks/consume' ? 'active' : '' ?>">
        <?= __('nav.consume') ?>
      </a>
      <?php endif; ?>
      <a href="/index.php?r=tasks/list" class="<?= strpos($_GET['r'] ?? '', 'tasks') === 0 ? 'active' : '' ?>">
        <?= __('task.list') ?>
      </a>
      <?php if (in_array(Auth::user()['role_key'], ['owner', 'manager'])): ?>
      <a href="/index.php?r=tasks/create" class="<?= ($_GET['r'] ?? '') === 'tasks/create' ? 'active' : '' ?>">
        <?= __('task.create') ?>
      </a>
      <?php endif; ?>
      <?php if (in_array(Auth::user()['role_key'], ['owner', 'manager', 'accountant'])): ?>
      <a href="/index.php?r=settings/categories" class="<?= strpos($_GET['r'] ?? '', 'settings') === 0 ? 'active' : '' ?>">
        <?= __('nav.settings') ?>
      </a>
      <?php endif; ?>
    </nav>
    <div class="hint"><?= __('app.title') ?></div>
  </aside>
  <div class="main" style="flex: 1; min-width: 0;">
    <div class="topbar">
      <div class="lang-switch">
        <a href="?<?= http_build_query(array_merge($_GET, ['lang' => 'zh'])) ?>">中文</a>
        <a href="?<?= http_build_query(array_merge($_GET, ['lang' => 'vi'])) ?>">Tiếng Việt</a>
      </div>
      <div class="user-box">
        <span><?= htmlspecialchars(Auth::user()['display_name']) ?></span>
        <a href="/index.php?r=auth/logout" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;">
          <?= __('nav.logout') ?>
        </a>
      </div>
    </div>
    <div class="container">
<?php else: ?>
<div class="container">
<?php endif; ?>
