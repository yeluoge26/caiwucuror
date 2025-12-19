<?php
$title = __('task.view');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('task.view') ?></h2>

<div class="card">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h3><?= htmlspecialchars($task['title']) ?></h3>
    <a href="/index.php?r=tasks/list" class="btn" style="background: #95a5a6;"><?= __('btn.back') ?></a>
  </div>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; margin-bottom: 20px;">
    <div>
      <strong><?= __('task.type') ?>:</strong>
      <span class="badge">
        <?= $task['type'] === 'fixed' ? __('task.type_fixed') : __('task.type_temporary') ?>
      </span>
    </div>
    <div>
      <strong><?= __('task.status') ?>:</strong>
      <?php
      $statusClass = [
        'pending' => 'badge-pending',
        'in_progress' => 'badge-approved',
        'completed' => 'badge-income',
        'approved' => 'badge-success',
        'rejected' => 'badge-void'
      ];
      $statusText = [
        'pending' => __('task.status_pending'),
        'in_progress' => __('task.status_in_progress'),
        'completed' => __('task.status_completed'),
        'approved' => __('task.status_approved'),
        'rejected' => __('task.status_rejected')
      ];
      ?>
      <span class="badge <?= $statusClass[$task['status']] ?? '' ?>">
        <?= $statusText[$task['status']] ?? $task['status'] ?>
      </span>
    </div>
    <div>
      <strong><?= __('task.due_date') ?>:</strong>
      <?= $task['due_date'] ? date('Y-m-d H:i', strtotime($task['due_date'])) : '-' ?>
    </div>
    <div>
      <strong><?= __('task.store') ?>:</strong>
      <?= htmlspecialchars($task['store'] ?? '-') ?>
    </div>
  </div>

  <div class="form-group">
    <strong><?= __('task.description') ?>:</strong>
    <div style="margin-top: 8px; padding: 12px; background: #f8f9fa; border-radius: 4px;">
      <?= nl2br(htmlspecialchars($task['description'] ?? '')) ?>
    </div>
  </div>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 20px;">
    <div>
      <strong><?= __('task.assign_user') ?>:</strong>
      <?= htmlspecialchars($task['assign_user_name'] ?? '-') ?>
    </div>
    <div>
      <strong><?= __('task.created_by') ?>:</strong>
      <?= htmlspecialchars($task['creator_name'] ?? '') ?>
    </div>
    <?php if ($task['completed_by']): ?>
    <div>
      <strong><?= __('task.completed_by') ?>:</strong>
      <?= htmlspecialchars($task['completed_by_name'] ?? '') ?>
      (<?= $task['completed_at'] ? date('Y-m-d H:i', strtotime($task['completed_at'])) : '' ?>)
    </div>
    <?php endif; ?>
    <?php if ($task['approved_by']): ?>
    <div>
      <strong><?= __('task.approved_by') ?>:</strong>
      <?= htmlspecialchars($task['approved_by_name'] ?? '') ?>
      (<?= $task['approved_at'] ? date('Y-m-d H:i', strtotime($task['approved_at'])) : '' ?>)
    </div>
    <?php endif; ?>
  </div>

  <?php if ($task['penalty_amount'] || $task['reward_amount']): ?>
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 20px;">
    <?php if ($task['penalty_amount']): ?>
    <div>
      <strong><?= __('task.penalty_amount') ?>:</strong>
      <span style="color: #e74c3c;">
        <?= number_format($task['penalty_amount'], 0, ',', '.') ?> <?= $task['currency'] ?>
      </span>
    </div>
    <?php endif; ?>
    <?php if ($task['reward_amount']): ?>
    <div>
      <strong><?= __('task.reward_amount') ?>:</strong>
      <span style="color: #27ae60;">
        <?= number_format($task['reward_amount'], 0, ',', '.') ?> <?= $task['currency'] ?>
      </span>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <?php if ($task['completion_note']): ?>
  <div class="form-group">
    <strong><?= __('task.completion_note') ?>:</strong>
    <div style="margin-top: 8px; padding: 12px; background: #f8f9fa; border-radius: 4px;">
      <?= nl2br(htmlspecialchars($task['completion_note'])) ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($task['approval_note']): ?>
  <div class="form-group">
    <strong><?= __('task.approval_note') ?>:</strong>
    <div style="margin-top: 8px; padding: 12px; background: #f8f9fa; border-radius: 4px;">
      <?= nl2br(htmlspecialchars($task['approval_note'])) ?>
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

  <div style="margin-top: 20px; display: flex; gap: 8px; flex-wrap: wrap;">
    <?php if ($task['assign_user_id'] == $user['id'] && $task['status'] === 'pending'): ?>
    <a href="/index.php?r=tasks/start&id=<?= $task['id'] ?>" class="btn btn-success">
      <?= __('task.start') ?>
    </a>
    <?php endif; ?>
    
    <?php if ($task['assign_user_id'] == $user['id'] && in_array($task['status'], ['pending', 'in_progress'])): ?>
    <a href="/index.php?r=tasks/complete&id=<?= $task['id'] ?>" class="btn btn-success">
      <?= __('task.complete') ?>
    </a>
    <?php endif; ?>
    
    <?php if ($user['role_key'] === 'owner' && $task['status'] === 'completed'): ?>
    <a href="/index.php?r=tasks/approve&id=<?= $task['id'] ?>" class="btn btn-success">
      <?= __('task.approve') ?>
    </a>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

