<?php
$title = __('tx.view');
include __DIR__ . '/../layout/header.php';
$lang = I18n::current();
?>

<h2 style="margin-bottom: 20px;"><?= __('tx.view') ?></h2>

<div class="card">
  <table>
    <tr>
      <th style="width: 150px;">ID</th>
      <td><?= $transaction['id'] ?></td>
    </tr>
    <tr>
      <th><?= __('tx.type') ?></th>
      <td>
        <span class="badge <?= $transaction['type'] === 'income' ? 'badge-income' : 'badge-expense' ?>">
          <?= $transaction['type'] === 'income' ? __('tx.income') : __('tx.expense') ?>
        </span>
      </td>
    </tr>
    <tr>
      <th><?= __('tx.amount') ?></th>
      <td style="font-size: 20px; font-weight: bold;">
        <?= number_format($transaction['amount'], 0, ',', '.') ?> <?= $transaction['currency'] ?>
      </td>
    </tr>
    <tr>
      <th><?= __('tx.category') ?></th>
      <td><?= htmlspecialchars($lang === 'zh' ? ($transaction['category_name_zh'] ?? '') : ($transaction['category_name_vi'] ?? '')) ?></td>
    </tr>
    <tr>
      <th><?= __('field.payment') ?></th>
      <td><?= htmlspecialchars($lang === 'zh' ? ($transaction['payment_name_zh'] ?? '') : ($transaction['payment_name_vi'] ?? '')) ?></td>
    </tr>
    <?php if ($transaction['vendor_name']): ?>
    <tr>
      <th><?= __('field.vendor') ?></th>
      <td><?= htmlspecialchars($transaction['vendor_name']) ?></td>
    </tr>
    <?php endif; ?>
    <tr>
      <th><?= __('field.time') ?></th>
      <td><?= date('d/m/Y H:i:s', strtotime($transaction['occurred_at'])) ?></td>
    </tr>
    <tr>
      <th><?= __('tx.status') ?></th>
      <td>
        <span class="badge badge-<?= $transaction['status'] ?>">
          <?php
          if ($transaction['status'] === 'approved') echo __('tx.approved');
          elseif ($transaction['status'] === 'pending') echo __('tx.pending');
          else echo __('tx.void');
          ?>
        </span>
      </td>
    </tr>
    <tr>
      <th><?= __('field.note') ?></th>
      <td><?= nl2br(htmlspecialchars($transaction['note'] ?? '')) ?></td>
    </tr>
    <tr>
      <th><?= __('tx.create') ?></th>
      <td><?= htmlspecialchars($transaction['creator_name'] ?? '') ?></td>
    </tr>
  </table>
  
  <?php if (!empty($attachments)): ?>
  <div style="margin-top: 20px;">
    <h3 style="margin-bottom: 12px;"><?= __('field.attachments') ?></h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 12px;">
      <?php foreach ($attachments as $att): ?>
      <div style="position: relative; aspect-ratio: 1; border-radius: 4px; overflow: hidden; border: 1px solid #ddd;">
        <a href="/<?= htmlspecialchars($att['file_path']) ?>" target="_blank" style="display: block; width: 100%; height: 100%;">
          <img src="/<?= htmlspecialchars($att['file_path']) ?>" 
               alt="Attachment" 
               style="width: 100%; height: 100%; object-fit: cover; cursor: pointer;">
        </a>
        <?php if (in_array($user['role_key'], ['owner', 'accountant']) || $att['uploaded_by'] == $user['id']): ?>
        <button onclick="deleteAttachment(<?= $att['id'] ?>)" 
                style="position: absolute; top: 4px; right: 4px; background: rgba(231, 76, 60, 0.9); color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; font-size: 18px; line-height: 1;">
          ×
        </button>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
  
  <div style="margin-top: 20px;">
    <a href="/index.php?r=transactions/list" class="btn"><?= __('btn.cancel') ?></a>
    <?php if (in_array($user['role_key'], ['owner', 'manager', 'accountant']) && $transaction['status'] === 'pending'): ?>
    <a href="/index.php?r=transactions/edit&id=<?= $transaction['id'] ?>" class="btn">
      <?= __('btn.edit') ?>
    </a>
    <?php endif; ?>
    <?php if ($user['role_key'] === 'owner' && $transaction['status'] === 'pending'): ?>
    <a href="/index.php?r=transactions/approve&id=<?= $transaction['id'] ?>" class="btn btn-success">
      <?= __('tx.approve') ?>
    </a>
    <?php endif; ?>
    <?php if (in_array($user['role_key'], ['owner', 'accountant']) && $transaction['status'] !== 'void'): ?>
    <a href="/index.php?r=transactions/void&id=<?= $transaction['id'] ?>" class="btn btn-danger">
      <?= __('btn.void') ?>
    </a>
    <?php endif; ?>
  </div>
</div>

<script>
function deleteAttachment(id) {
  if (!confirm('<?= __('upload.delete_confirm') ?>')) {
    return;
  }
  
  fetch('/index.php?r=transactions/deleteAttachment&id=' + id, {
    method: 'GET',
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      location.reload();
    } else {
      alert('<?= __('upload.delete_failed') ?>');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('<?= __('upload.delete_failed') ?>');
  });
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>

