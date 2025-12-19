<?php
$title = __('nav.assets');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('nav.assets') ?></h2>

<?php $canManage = in_array(Auth::user()['role_key'], ['owner', 'manager', 'accountant']); ?>
<?php if ($canManage): ?>
  <div style="margin-bottom: 12px; display: flex; gap: 8px; flex-wrap: wrap;">
    <a href="/index.php?r=assets/create" class="btn btn-success"><?= __('asset.add') ?></a>
    <a href="/index.php?r=assets/export" class="btn"><?= __('asset.export') ?></a>
    <form method="post" action="/index.php?r=assets/import" enctype="multipart/form-data" style="display: inline-flex; gap: 8px; align-items: center;">
      <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
      <input type="file" name="asset_csv" accept=".csv" required style="font-size: 12px;">
      <button type="submit" class="btn"><?= __('asset.import') ?></button>
    </form>
  </div>
<?php endif; ?>

<?php if (isset($_GET['import'])): ?>
  <div class="alert <?= ($_GET['failed'] ?? 0) > 0 ? 'alert-error' : 'alert-success' ?>" style="margin-bottom: 12px;">
    <?= sprintf(__('asset.import_result'), intval($_GET['success'] ?? 0), intval($_GET['failed'] ?? 0)) ?>
  </div>
<?php endif; ?>

<div class="card">
  <h3 style="margin-bottom: 16px;"><?= __('asset.list') ?></h3>
  <table>
    <tr>
      <th>ID</th>
      <th><?= __('asset.store') ?></th>
      <th><?= __('asset.subcategory') ?></th>
      <th><?= __('asset.name') ?></th>
      <th><?= __('asset.floor') ?></th>
      <th><?= __('asset.location') ?></th>
      <th><?= __('asset.price') ?></th>
      <th><?= __('asset.quantity') ?></th>
      <th><?= __('asset.acquired_at') ?></th>
      <th><?= __('asset.photos') ?></th>
      <th><?= __('field.note') ?></th>
      <th><?= __('tx.create') ?></th>
      <th><?= __('list.actions') ?></th>
    </tr>
    <?php if (empty($items)): ?>
    <tr>
      <td colspan="13" style="text-align: center; color: #888;"><?= __('list.no_data') ?></td>
    </tr>
    <?php else: ?>
    <?php foreach ($items as $row): ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td>
        <?php
          $map = [
            'coffee' => __('asset.category_coffee'),
            'office' => __('asset.category_office'),
            'whiskey' => __('asset.category_whiskey')
          ];
          echo htmlspecialchars($map[$row['category']] ?? $row['category']);
        ?>
      </td>
      <td><?= htmlspecialchars($row['subcategory'] ?? '-') ?></td>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= htmlspecialchars($row['floor'] ?? '-') ?></td>
      <td><?= htmlspecialchars($row['location'] ?? '-') ?></td>
      <td><?= number_format($row['price'], 0, ',', '.') ?> ₫</td>
      <td><?= number_format($row['quantity'] ?? 0, 0, '.', ',') ?></td>
      <td><?= date('Y-m-d', strtotime($row['acquired_at'])) ?></td>
      <td>
        <?php if (!empty($row['thumb_path'])): ?>
          <img src="/<?= htmlspecialchars($row['thumb_path']) ?>" alt="thumb" style="max-height:60px; object-fit: contain;">
        <?php else: ?>
          -
        <?php endif; ?>
      </td>
      <td><?= htmlspecialchars($row['note'] ?? '') ?></td>
      <td><?= htmlspecialchars($row['creator_name'] ?? '') ?></td>
      <td>
        <a href="/index.php?r=assets/view&id=<?= $row['id'] ?>" class="btn" style="padding:6px 10px; font-size:12px;"><?= __('asset.detail') ?></a>
        <?php if ($canManage && $row['status'] !== 'void'): ?>
        <a href="/index.php?r=assets/void&id=<?= $row['id'] ?>" class="btn btn-danger" style="margin-left:6px; padding:6px 10px; font-size:12px;" onclick="return confirm('<?= __('asset.void_confirm') ?>');"><?= __('btn.void') ?></a>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
  </table>
</div>
<?php include __DIR__ . '/../layout/footer.php'; ?>
