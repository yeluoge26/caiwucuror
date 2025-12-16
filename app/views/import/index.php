<?php
$title = __('import.title');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('import.title') ?></h2>

<div class="card">
  <?php if (isset($error)): ?>
  <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  
  <?php if (isset($success)): ?>
  <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>
  
  <h3 style="margin-bottom: 16px;"><?= __('import.instructions') ?></h3>
  <div style="margin-bottom: 20px; padding: 16px; background: #f8f9fa; border-radius: 4px;">
    <p><?= __('import.instructions_text') ?></p>
    <ol style="margin-left: 20px; margin-top: 12px;">
      <li><?= __('import.step1') ?></li>
      <li><?= __('import.step2') ?></li>
      <li><?= __('import.step3') ?></li>
      <li><?= __('import.step4') ?></li>
    </ol>
    <p style="margin-top: 12px;">
      <a href="/index.php?r=export/csv" class="btn" style="padding: 6px 12px; font-size: 12px;">
        <?= __('import.download_template') ?>
      </a>
    </p>
  </div>
  
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    
    <div class="form-group">
      <label><?= __('import.select_file') ?></label>
      <input type="file" name="file" accept=".csv" required>
      <small style="color: #666; display: block; margin-top: 4px;">
        <?= __('import.file_format') ?>
      </small>
    </div>
    
    <div class="form-group">
      <button type="submit" class="btn btn-success"><?= __('import.submit') ?></button>
      <a href="/index.php?r=transactions/list" class="btn"><?= __('btn.cancel') ?></a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

