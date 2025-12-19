<?php
$title = __('task.complete');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('task.complete') ?></h2>

<div class="card">
  <div style="margin-bottom: 20px;">
    <h3><?= htmlspecialchars($task['title']) ?></h3>
    <p><?= nl2br(htmlspecialchars($task['description'] ?? '')) ?></p>
  </div>

  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">

    <div class="form-group">
      <label><?= __('task.completion_note') ?></label>
      <textarea name="completion_note" rows="4" placeholder="<?= __('task.completion_note') ?>"></textarea>
    </div>

    <?php if ($task['require_photo'] || $task['require_attachment']): ?>
    <div class="form-group">
      <label>
        <?php if ($task['require_photo']): ?>
          <?= __('task.require_photo') ?> *
        <?php else: ?>
          <?= __('task.require_attachment') ?>
        <?php endif; ?>
      </label>
      <input type="file" name="attachments[]" multiple accept="image/*,application/pdf,.doc,.docx">
      <small style="color: #666;">支持图片、PDF、Word文档，最大10MB</small>
    </div>
    <?php endif; ?>

    <div style="margin-top: 20px;">
      <button type="submit" class="btn btn-success"><?= __('btn.submit') ?></button>
      <a href="/index.php?r=tasks/view&id=<?= $task['id'] ?>" class="btn" style="background: #95a5a6;"><?= __('btn.cancel') ?></a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

