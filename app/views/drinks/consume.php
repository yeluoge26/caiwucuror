<?php
$title = __('drink.consume');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('drink.consume') ?></h2>

<div class="card">
  <?php if (!empty($error)): ?>
  <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <?php if (!empty($success)): ?>
  <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>
  <form method="post">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    <div class="form-group">
      <label><?= __('drink.select') ?></label>
      <select name="drink_id" onchange="this.form.submit()">
        <?php foreach ($drinks as $d): ?>
        <option value="<?= $d['id'] ?>" <?= $selectedId == $d['id'] ? 'selected' : '' ?>><?= htmlspecialchars($d['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label><?= __('material.quantity') ?></label>
      <input type="number" name="quantity" min="1" step="1" required>
    </div>
    <div class="form-group">
      <label><?= __('field.time') ?></label>
      <input type="date" name="occurred_at" value="<?= date('Y-m-d') ?>" required>
    </div>
    <div class="form-group">
      <label><?= __('field.note') ?></label>
      <textarea name="note"></textarea>
    </div>
    <div class="form-group">
      <button type="submit" class="btn btn-success"><?= __('drink.consume') ?></button>
    </div>
  </form>
</div>

<div class="card">
  <h3 style="margin-bottom: 12px;"><?= __('drink.recipe_current') ?></h3>
  <?php if (empty($recipe)): ?>
    <div style="color:#888;"><?= __('list.no_data') ?></div>
  <?php else: ?>
  <table>
    <tr>
      <th><?= __('material.name') ?></th>
      <th><?= __('material.unit') ?></th>
      <th><?= __('material.quantity') ?></th>
    </tr>
    <?php foreach ($recipe as $line): ?>
    <tr>
      <td><?= htmlspecialchars($line['material_name']) ?></td>
      <td><?= htmlspecialchars($line['unit']) ?></td>
      <td><?= number_format($line['amount'], 2, '.', ',') ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
