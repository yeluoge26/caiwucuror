<?php
$title = __('task.approve');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('task.approve') ?></h2>

<div class="card">
  <div style="margin-bottom: 20px;">
    <h3><?= htmlspecialchars($task['title']) ?></h3>
    <p><?= nl2br(htmlspecialchars($task['description'] ?? '')) ?></p>
  </div>

  <?php if ($task['completion_note']): ?>
  <div class="form-group">
    <strong><?= __('task.completion_note') ?>:</strong>
    <div style="margin-top: 8px; padding: 12px; background: #f8f9fa; border-radius: 4px;">
      <?= nl2br(htmlspecialchars($task['completion_note'])) ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if (!empty($attachments)): ?>
  <div class="form-group">
    <strong><?= __('task.require_attachment') ?>:</strong>
    <div style="margin-top: 8px; display: flex; flex-wrap: wrap; gap: 8px;">
      <?php foreach ($attachments as $att): ?>
        <?php if (strpos($att['file_type'], 'image/') === 0): ?>
        <a href="/<?= htmlspecialchars($att['file_path']) ?>" target="_blank">
          <img src="/<?= htmlspecialchars($att['file_path']) ?>" alt="附件" 
               style="max-width: 150px; max-height: 150px; border: 1px solid #ddd; border-radius: 4px;">
        </a>
        <?php else: ?>
        <a href="/<?= htmlspecialchars($att['file_path']) ?>" target="_blank" class="btn" 
           style="padding: 6px 12px; font-size: 12px;">
          下载文件
        </a>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <form method="post">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">

    <div class="form-group">
      <label><?= __('task.approval_note') ?></label>
      <textarea name="approval_note" rows="4" placeholder="<?= __('task.approval_note') ?>"></textarea>
    </div>

    <div style="margin-top: 20px; display: flex; gap: 8px;">
      <button type="submit" name="action" value="approve" class="btn btn-success">
        <?= __('task.approve') ?>
      </button>
      <button type="submit" name="action" value="reject" class="btn btn-danger">
        <?= __('task.reject') ?>
      </button>
      <a href="/index.php?r=tasks/view&id=<?= $task['id'] ?>" class="btn" style="background: #95a5a6;"><?= __('btn.cancel') ?></a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

