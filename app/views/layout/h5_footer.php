</div>
<?php if (Auth::check() && Auth::user()['role_key'] === 'manager'): ?>
<nav class="h5-bottom-nav">
  <a href="/index.php?r=manager/dashboard" class="h5-nav-item <?= ($_GET['r'] ?? '') === 'manager/dashboard' ? 'active' : '' ?>">
    <span class="icon">🏠</span>
    <span><?= __('nav.home') ?></span>
  </a>
  <a href="/index.php?r=inspections/create" class="h5-nav-item <?= strpos($_GET['r'] ?? '', 'inspections') === 0 ? 'active' : '' ?>">
    <span class="icon">🔍</span>
    <span><?= __('nav.inspections') ?></span>
  </a>
  <a href="/index.php?r=transactions/create" class="h5-nav-item <?= strpos($_GET['r'] ?? '', 'transactions') === 0 ? 'active' : '' ?>">
    <span class="icon">✏️</span>
    <span><?= __('nav.add') ?></span>
  </a>
  <a href="/index.php?r=auth/profile" class="h5-nav-item <?= strpos($_GET['r'] ?? '', 'auth/profile') === 0 ? 'active' : '' ?>">
    <span class="icon">👤</span>
    <span><?= __('nav.profile') ?></span>
  </a>
</nav>
<?php endif; ?>
</body>
</html>

