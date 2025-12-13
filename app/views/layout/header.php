<!doctype html>
<html lang="<?= I18n::current() ?>">
<head>
<meta charset="utf-8">
<title><?= $title ?? __('app.title') ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; 
         background: #f5f5f5; color: #333; line-height: 1.6; }
  .container { max-width: 1200px; margin: 0 auto; padding: 12px; }
  .header { background: #2c3e50; color: white; padding: 16px; margin-bottom: 20px; }
  .header h1 { font-size: 20px; margin-bottom: 12px; }
  .nav { display: flex; flex-wrap: wrap; gap: 12px; }
  .nav a { color: white; text-decoration: none; padding: 8px 16px; background: rgba(255,255,255,0.1); 
           border-radius: 4px; transition: background 0.2s; }
  .nav a:hover { background: rgba(255,255,255,0.2); }
  .nav a.active { background: rgba(255,255,255,0.3); }
  .lang-switch { margin-left: auto; }
  .lang-switch a { color: white; text-decoration: none; margin-left: 8px; }
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
  @media (max-width: 768px) {
    .nav { flex-direction: column; }
    .container { padding: 8px; }
    table { font-size: 12px; }
    table th, table td { padding: 8px; }
  }
</style>
</head>
<body>
<?php if (Auth::check()): ?>
<div class="header">
  <div class="container">
    <h1><?= __('app.name') ?></h1>
    <div style="display: flex; align-items: center; justify-content: space-between;">
      <nav class="nav">
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
        <a href="/index.php?r=settings/categories" class="<?= strpos($_GET['r'] ?? '', 'settings') === 0 ? 'active' : '' ?>">
          <?= __('nav.settings') ?>
        </a>
        <?php endif; ?>
      </nav>
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="lang-switch">
          <a href="?<?= http_build_query(array_merge($_GET, ['lang' => 'zh'])) ?>">中文</a>
          <a href="?<?= http_build_query(array_merge($_GET, ['lang' => 'vi'])) ?>">Tiếng Việt</a>
        </div>
        <span><?= htmlspecialchars(Auth::user()['display_name']) ?></span>
        <a href="/index.php?r=auth/logout" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;">
          <?= __('nav.logout') ?>
        </a>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
<div class="container">

