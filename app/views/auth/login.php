<?php
$title = __('login.title');
include __DIR__ . '/../layout/header.php';
?>

<style>
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

.login-logo-icon {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 40px;
  margin: 0 auto 16px;
  box-shadow: 0 4px 12px rgba(139, 69, 19, 0.3);
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

.login-form .form-group {
  margin-bottom: 20px;
}

.login-form .form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  font-size: 14px;
  color: #374151;
}

.login-form .form-group input {
  width: 100%;
  padding: 14px 16px;
  border: 2px solid #E5E7EB;
  border-radius: 10px;
  font-size: 15px;
  transition: all 0.2s ease;
}

.login-form .form-group input:focus {
  outline: none;
  border-color: #8B4513;
  box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
}

.login-form .btn {
  width: 100%;
  padding: 14px;
  font-size: 16px;
  margin-top: 8px;
}

.login-lang-switch {
  text-align: center;
  margin-top: 24px;
  padding-top: 24px;
  border-top: 1px solid #E5E7EB;
}

.login-lang-switch a {
  color: #8B4513;
  text-decoration: none;
  margin: 0 8px;
  font-weight: 500;
  transition: color 0.2s ease;
}

.login-lang-switch a:hover {
  color: #A0522D;
  text-decoration: underline;
}
</style>

<div class="login-page">
  <div class="login-card">
    <div class="login-logo">
      <div class="login-logo-icon">☕</div>
      <div class="login-title"><?= __('login.title') ?></div>
      <div class="login-subtitle"><?= __('app.name') ?></div>
    </div>
    
    <?php if (isset($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="post" class="login-form">
      <div class="form-group">
        <label><?= __('login.username') ?></label>
        <input type="text" name="username" required autofocus placeholder="<?= __('login.username') ?>">
      </div>
      
      <div class="form-group">
        <label><?= __('login.password') ?></label>
        <input type="password" name="password" required placeholder="<?= __('login.password') ?>">
      </div>
      
      <div class="form-group">
        <button type="submit" class="btn btn-primary"><?= __('login.submit') ?></button>
      </div>
    </form>
    
    <div class="login-lang-switch">
      <a href="?lang=zh">中文</a>
      <span>|</span>
      <a href="?lang=vi">Tiếng Việt</a>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

