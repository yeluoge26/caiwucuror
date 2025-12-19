<?php
$title = __('material.title');
include __DIR__ . '/../layout/header.php';
$lang = I18n::current();
?>

<h2 style="margin-bottom: 20px;"><?= __('material.title') ?></h2>

<?php if (in_array(Auth::user()['role_key'], ['owner', 'manager', 'accountant'])): ?>
<div style="margin-bottom: 12px;">
  <a href="/index.php?r=materials/create" class="btn btn-success"><?= __('material.add') ?></a>
</div>
<?php endif; ?>

<div class="card">
  <h3 style="margin-bottom: 16px;"><?= __('material.list') ?></h3>
  <table>
    <tr>
      <th><?= __('material.name') ?></th>
      <th><?= __('material.store') ?></th>
      <th><?= __('material.category') ?></th>
      <th><?= __('material.unit') ?></th>
      <th><?= __('material.quantity') ?></th>
      <th><?= __('material.min_quantity') ?></th>
      <th><?= __('field.note') ?></th>
    </tr>
    <?php if (empty($items)): ?>
    <tr>
      <td colspan="7" style="text-align:center; color:#888;"><?= __('list.no_data') ?></td>
    </tr>
    <?php else: ?>
    <?php foreach ($items as $row): ?>
    <tr>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td>
        <?php
        $storeMap = [
          'coffee' => __('asset.category_coffee'),
          'office' => __('asset.category_office'),
          'whiskey' => __('asset.category_whiskey')
        ];
        $stores = array_map('trim', explode('/', $row['store_list']));
        $stores = array_map(function($s) use ($storeMap) {
          return $storeMap[$s] ?? $s;
        }, $stores);
        echo htmlspecialchars(implode(' / ', $stores));
        ?>
      </td>
      <td>
        <?php
        $catMap = [
          'bean' => __('material.cat_bean'),
          'milk' => __('material.cat_milk'),
          'syrup' => __('material.cat_syrup'),
          'consumable' => __('material.cat_consumable')
        ];
        echo htmlspecialchars($catMap[$row['category']] ?? $row['category']);
        ?>
      </td>
      <td><?= htmlspecialchars($row['unit']) ?></td>
      <td><?= number_format($row['quantity'], 2, '.', ',') ?></td>
      <td><?= number_format($row['min_quantity'], 2, '.', ',') ?></td>
      <td><?= htmlspecialchars($row['note'] ?? '') ?></td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
  </table>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
