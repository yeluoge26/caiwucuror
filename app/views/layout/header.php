<!doctype html>
<html lang="<?= I18n::current() ?>">
<head>
<meta charset="utf-8">
<title><?= $title ?? __('app.title') ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="<?= __('app.title') ?>">
<meta name="keywords" content="Teah Space, tài chính, 财务, 管理系统, finance">
<link rel="stylesheet" href="/assets/css/pc-style.css">
</head>
<body>
<?php if (Auth::check()): ?>
<div class="layout">
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-icon">☕</div>
      <span><?= __('app.name') ?></span>
    </div>
    <nav class="nav-vertical">
      <?php if (Auth::user()['role_key'] === 'manager'): ?>
      <a href="/index.php?r=manager/dashboard" class="<?= ($_GET['r'] ?? '') === 'manager/dashboard' ? 'active' : '' ?>">
        <?= __('manager.dashboard') ?>
      </a>
      <?php else: ?>
      <a href="/index.php?r=reports/dashboard" class="<?= ($_GET['r'] ?? '') === 'reports/dashboard' ? 'active' : '' ?>">
        <?= __('nav.dashboard') ?>
      </a>
      <?php endif; ?>
      <a href="/index.php?r=transactions/create" class="<?= ($_GET['r'] ?? '') === 'transactions/create' ? 'active' : '' ?>">
        <?= __('nav.add') ?>
      </a>
      <a href="/index.php?r=transactions/list" class="<?= ($_GET['r'] ?? '') === 'transactions/list' ? 'active' : '' ?>">
        <?= __('nav.list') ?>
      </a>
      <?php if (in_array(Auth::user()['role_key'], ['owner', 'manager', 'accountant'])): ?>
      <?php if (Auth::user()['role_key'] === 'manager'): ?>
      <a href="/index.php?r=cash_closings/create" class="<?= strpos($_GET['r'] ?? '', 'cash_closings') === 0 ? 'active' : '' ?>">
        <?= __('cash_closing.create') ?>
      </a>
      <a href="/index.php?r=cash_closings/list" class="<?= ($_GET['r'] ?? '') === 'cash_closings/list' ? 'active' : '' ?>">
        <?= __('cash_closing.list') ?>
      </a>
      <?php endif; ?>
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
      <a href="/index.php?r=equipment_rentals/list" class="<?= strpos($_GET['r'] ?? '', 'equipment_rentals') === 0 ? 'active' : '' ?>">
        <?= __('equipment_rental.list') ?>
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
      <a href="/index.php?r=employees/list" class="<?= strpos($_GET['r'] ?? '', 'employees') === 0 ? 'active' : '' ?>">
        <?= __('employee.list') ?>
      </a>
      <a href="/index.php?r=shifts/list" class="<?= strpos($_GET['r'] ?? '', 'shifts') === 0 && ($_GET['r'] ?? '') !== 'shifts/weeklySchedule' ? 'active' : '' ?>">
        <?= __('shift.list') ?>
      </a>
      <a href="/index.php?r=shifts/weeklySchedule" class="<?= ($_GET['r'] ?? '') === 'shifts/weeklySchedule' ? 'active' : '' ?>">
        <?= __('shift.weekly_schedule') ?>
      </a>
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
