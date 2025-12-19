<?php
$title = __('drink.recipes');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('drink.recipes') ?></h2>

<div class="card">
  <h3 style="margin-bottom: 12px;"><?= __('drink.create') ?></h3>
  <?php if (!empty($error)): ?>
  <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="post" style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    <input type="hidden" name="action" value="create_drink">
    <div style="min-width:200px;">
      <label><?= __('drink.name') ?></label>
      <input type="text" name="name" required>
    </div>
    <div style="min-width:180px;">
      <label><?= __('material.store') ?></label>
      <select name="store" required>
        <option value="coffee"><?= __('asset.category_coffee') ?></option>
        <option value="office"><?= __('asset.category_office') ?></option>
        <option value="whiskey"><?= __('asset.category_whiskey') ?></option>
      </select>
    </div>
    <button type="submit" class="btn btn-success"><?= __('btn.save') ?></button>
  </form>
</div>

<div class="card">
  <h3 style="margin-bottom: 12px;"><?= __('drink.recipe_manage') ?></h3>
  <form method="get" style="margin-bottom:12px;">
    <input type="hidden" name="r" value="drinks/recipes">
    <label><?= __('drink.select') ?></label>
    <select name="drink_id" onchange="this.form.submit()">
      <?php foreach ($drinks as $d): ?>
      <option value="<?= $d['id'] ?>" <?= $selectedId == $d['id'] ? 'selected' : '' ?>><?= htmlspecialchars($d['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </form>

  <?php if ($selectedId): ?>
  <form method="post">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    <input type="hidden" name="action" value="save_recipe">
    <input type="hidden" name="drink_id" value="<?= $selectedId ?>">
    <div style="margin-bottom: 8px;">
      <label style="font-size:13px; color:#555;"><?= __('material.filter') ?></label>
      <input type="text" id="material-filter" placeholder="<?= __('material.filter_placeholder') ?>" style="padding:6px; border:1px solid #ddd; border-radius:4px; width:240px;">
    </div>
    <table>
      <tr>
        <th><?= __('material.name') ?></th>
        <th><?= __('material.unit') ?></th>
        <th><?= __('material.quantity') ?> (g / ml)</th>
      </tr>
      <tbody id="recipe-rows">
        <?php if (!empty($recipe)): ?>
          <?php foreach ($recipe as $line): ?>
          <tr>
            <td>
              <select name="material_id[]" class="material-select" required>
                <?php foreach ($materials as $m): ?>
                <option value="<?= $m['id'] ?>" <?= $line['material_id'] == $m['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($m['name']) ?> (<?= htmlspecialchars($m['unit']) ?>)
                </option>
                <?php endforeach; ?>
              </select>
            </td>
            <td><?= htmlspecialchars($line['unit']) ?></td>
            <td><input type="number" name="amount[]" step="0.01" min="0" value="<?= htmlspecialchars($line['amount']) ?>" placeholder="<?= __('drink.amount_hint') ?>" required></td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
    <div style="margin-top: 8px;">
      <button type="button" class="btn" onclick="addRow()"><?= __('drink.add_row') ?></button>
      <button type="submit" class="btn btn-success"><?= __('btn.save') ?></button>
    </div>
  </form>
  <?php else: ?>
  <div style="color:#888;"><?= __('list.no_data') ?></div>
  <?php endif; ?>
</div>

<script>
function addRow() {
  const tbody = document.getElementById('recipe-rows');
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td>
      <select name="material_id[]" class="material-select" required>
        <?php foreach ($materials as $m): ?>
        <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?> (<?= htmlspecialchars($m['unit']) ?>)</option>
        <?php endforeach; ?>
      </select>
    </td>
    <td></td>
    <td><input type="number" name="amount[]" step="0.01" min="0" placeholder="<?= __('drink.amount_hint') ?>" required></td>
  `;
  tbody.appendChild(tr);
}

// filter material options by name
const filterInput = document.getElementById('material-filter');
filterInput.addEventListener('input', () => {
  const term = filterInput.value.toLowerCase();
  document.querySelectorAll('.material-select').forEach(sel => {
    Array.from(sel.options).forEach(opt => {
      const text = opt.textContent.toLowerCase();
      opt.style.display = text.includes(term) ? '' : 'none';
    });
  });
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
