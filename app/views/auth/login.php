<?php
$title = __('login.title');
include __DIR__ . '/../layout/header.php';
?>

<div style="max-width: 400px; margin: 60px auto;">
  <div class="card">
    <h2 style="margin-bottom: 24px; text-align: center;"><?= __('login.title') ?></h2>
    
    <?php if (isset($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="post">
      <div class="form-group">
        <label><?= __('login.username') ?></label>
        <input type="text" name="username" required autofocus>
      </div>
      
      <div class="form-group">
        <label><?= __('login.password') ?></label>
        <input type="password" name="password" required>
      </div>
      
      <div class="form-group">
        <button type="submit" class="btn" style="width: 100%;"><?= __('login.submit') ?></button>
      </div>
    </form>
    
    <div style="text-align: center; margin-top: 16px;">
      <a href="?lang=zh">中文</a> | <a href="?lang=vi">Tiếng Việt</a>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

