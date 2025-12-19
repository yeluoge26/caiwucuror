<?php
$title = __('asset.detail');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('asset.detail') ?></h2>

<div class="card">
  <table>
    <tr><th style="width:160px;">ID</th><td><?= $asset['id'] ?></td></tr>
    <tr><th><?= __('asset.store') ?></th><td>
      <?php
        $map = [
          'coffee' => __('asset.category_coffee'),
          'office' => __('asset.category_office'),
          'whiskey' => __('asset.category_whiskey')
        ];
        echo htmlspecialchars($map[$asset['category']] ?? $asset['category']);
      ?>
    </td></tr>
    <tr><th><?= __('asset.subcategory') ?></th><td><?= htmlspecialchars($asset['subcategory'] ?? '-') ?></td></tr>
    <tr><th><?= __('asset.name') ?></th><td><?= htmlspecialchars($asset['name'] ?? '-') ?></td></tr>
    <tr><th><?= __('asset.floor') ?></th><td><?= htmlspecialchars($asset['floor'] ?? '-') ?></td></tr>
    <tr><th><?= __('asset.location') ?></th><td><?= htmlspecialchars($asset['location'] ?? '-') ?></td></tr>
    <tr><th><?= __('asset.price') ?></th><td><?= number_format($asset['price'], 0, ',', '.') ?> ₫</td></tr>
    <tr><th><?= __('asset.quantity') ?></th><td><?= number_format($asset['quantity'] ?? 0, 0, '.', ',') ?></td></tr>
    <tr><th><?= __('asset.acquired_at') ?></th><td><?= date('Y-m-d', strtotime($asset['acquired_at'])) ?></td></tr>
    <tr><th><?= __('asset.status') ?></th><td>
      <span class="badge <?= $asset['status'] === 'void' ? 'badge-void' : 'badge-approved' ?>">
        <?= $asset['status'] === 'void' ? __('asset.voided') : __('asset.active') ?>
      </span>
    </td></tr>
    <tr><th><?= __('field.note') ?></th><td><?= nl2br(htmlspecialchars($asset['note'] ?? '')) ?></td></tr>
    <tr><th><?= __('tx.create') ?></th><td><?= htmlspecialchars($asset['creator_name'] ?? '') ?></td></tr>
  </table>

  <div style="margin-top: 16px;">
    <a href="/index.php?r=assets/list" class="btn"><?= __('btn.cancel') ?></a>
    <?php if (in_array(Auth::user()['role_key'], ['owner', 'manager', 'accountant']) && $asset['status'] !== 'void'): ?>
    <a href="/index.php?r=assets/void&id=<?= $asset['id'] ?>" class="btn btn-danger"><?= __('btn.void') ?></a>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <h3 style="margin-bottom: 12px;"><?= __('asset.photos') ?></h3>
  <?php if (!empty($attachments)): ?>
  <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(160px,1fr)); gap:12px;">
    <?php foreach ($attachments as $att): ?>
    <div style="border:1px solid #eee; padding:8px; border-radius:6px; text-align:center; background:#fff;">
      <a href="/<?= htmlspecialchars($att['file_path']) ?>" target="_blank">
        <img src="/<?= htmlspecialchars($att['file_path']) ?>" style="max-width:100%; max-height:140px; object-fit:contain;" alt="asset">
      </a>
      <div style="font-size:12px; color:#666; margin-top:4px;"><?= date('Y-m-d H:i', strtotime($att['created_at'])) ?></div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <div style="color:#888;"><?= __('attachment.none') ?></div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
