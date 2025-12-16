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
  .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 16px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
  .header h1 { font-size: 20px; margin-bottom: 12px; font-weight: 600; }
  .nav { display: flex; flex-wrap: wrap; gap: 12px; }
  .nav a { color: white; text-decoration: none; padding: 8px 16px; background: rgba(255,255,255,0.15); 
           border-radius: 6px; transition: all 0.3s; font-weight: 500; }
  .nav a:hover { background: rgba(255,255,255,0.25); transform: translateY(-1px); }
  .nav a.active { background: rgba(255,255,255,0.3); box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
  .lang-switch { margin-left: auto; }
  .lang-switch a { color: white; text-decoration: none; margin-left: 8px; }
  .card { background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: box-shadow 0.3s; }
  .card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
  .btn { display: inline-block; padding: 10px 20px; background: #3498db; color: white; 
         text-decoration: none; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; 
         transition: all 0.3s; font-weight: 500; }
  .btn:hover { background: #2980b9; transform: translateY(-1px); box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
  .btn:active { transform: translateY(0); }
  .btn-danger { background: #e74c3c; }
  .btn-danger:hover { background: #c0392b; }
  .btn-success { background: #27ae60; }
  .btn-success:hover { background: #229954; }
  .form-group { margin-bottom: 16px; }
  .form-group label { display: block; margin-bottom: 6px; font-weight: 500; }
  .form-group input, .form-group select, .form-group textarea { 
    width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; 
    transition: border-color 0.3s, box-shadow 0.3s; }
  .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
    outline: none; border-color: #3498db; box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1); }
  .form-group textarea { min-height: 80px; resize: vertical; }
  table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
  table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #e9ecef; }
  table th { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 600; }
  table tr:hover { background: #f8f9fa; transition: background 0.2s; }
  table tr:last-child td { border-bottom: none; }
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
    table { font-size: 12px; display: block; overflow-x: auto; white-space: nowrap; }
    table th, table td { padding: 8px 4px; }
    table th { background: #667eea !important; } /* 移动端简化表头样式 */
    .header { padding: 12px; }
    .header h1 { font-size: 18px; }
    .header .container > div { flex-direction: column; align-items: flex-start !important; gap: 8px; }
    .header .nav { width: 100%; }
    .header .nav a { flex: 1; text-align: center; padding: 10px 12px; }
    .lang-switch { margin-left: 0 !important; }
    .card { padding: 12px; border-radius: 6px; }
    .btn { padding: 10px 16px; font-size: 14px; min-height: 44px; } /* 触摸友好 */
    .form-group input, .form-group select, .form-group textarea { 
      font-size: 16px; /* 防止iOS缩放 */
      padding: 12px; /* 更大的触摸区域 */
      border-radius: 6px;
    }
    .form-group input[type="file"] { font-size: 16px !important; }
    #image-preview { grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)) !important; }
    .kpi { font-size: 20px; }
    h2 { font-size: 18px; }
    h3 { font-size: 16px; }
    /* 模态框移动端优化 */
    #vendor-modal .card { margin: 10px; max-width: calc(100% - 20px); }
    /* 图表移动端优化 */
    canvas { max-height: 250px !important; }
  }
  
  @media (max-width: 480px) {
    .container { padding: 4px; }
    .card { padding: 10px; margin-bottom: 12px; }
    table { font-size: 11px; }
    .btn { padding: 10px 14px; font-size: 13px; min-height: 44px; }
    .kpi { font-size: 18px; }
    .header h1 { font-size: 16px; }
    .nav a { font-size: 13px; padding: 8px 10px; }
    /* 高级报表移动端 */
    #report-filter-form { flex-direction: column; }
    #report-filter-form .form-group { width: 100%; }
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
        <a href="/index.php?r=advancedReports/index" class="<?= strpos($_GET['r'] ?? '', 'advancedReports') === 0 ? 'active' : '' ?>">
          <?= __('nav.advanced_reports') ?>
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

