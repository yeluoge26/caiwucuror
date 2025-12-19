<?php
$title = __('btn.void');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('btn.void') ?></h2>

<div class="card">
  <div class="alert alert-error"><?= __('asset.void_confirm') ?></div>
  <table>
    <tr><th style="width:160px;">ID</th><td><?= $asset['id'] ?></td></tr>
    <tr><th><?= __('asset.name') ?></th><td><?= htmlspecialchars($asset['name']) ?></td></tr>
    <tr><th><?= __('asset.store') ?></th><td><?= htmlspecialchars($asset['category']) ?></td></tr>
    <tr><th><?= __('asset.quantity') ?></th><td><?= number_format($asset['quantity'] ?? 0, 0, '.', ',') ?></td></tr>
  </table>
  <form method="post" style="margin-top: 16px;">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    <button type="submit" class="btn btn-danger"><?= __('btn.void') ?></button>
    <a href="/index.php?r=assets/view&id=<?= $asset['id'] ?>" class="btn"><?= __('btn.cancel') ?></a>
  </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
