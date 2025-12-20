<?php
$title = __('shift.schedule');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('shift.schedule') ?></h2>

<?php if ($error): ?>
<div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
  <form method="post" id="scheduleForm">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    <input type="hidden" name="shift_date" value="<?= htmlspecialchars($date) ?>">
    
    <div class="form-group">
      <label><?= __('shift.shift_date') ?> *</label>
      <input type="date" name="shift_date" id="shift_date" value="<?= htmlspecialchars($date) ?>" 
             onchange="location.href='/index.php?r=shifts/schedule&date='+this.value" required>
    </div>

    <div style="margin-bottom: 20px;">
      <button type="button" class="btn" onclick="addShiftRow()"><?= __('shift.add_shift') ?></button>
      <button type="button" class="btn" onclick="clearAll()" style="background: #95a5a6;"><?= __('btn.clear') ?></button>
    </div>

    <div id="shiftsContainer">
      <?php
      // 显示已有班次
      $shiftIndex = 0;
      $shiftTypes = ['morning', 'afternoon', 'evening'];
      $typeText = [
        'morning' => __('shift.type_morning'),
        'afternoon' => __('shift.type_afternoon'),
        'evening' => __('shift.type_evening')
      ];
      
      foreach ($existingShifts as $shift):
      ?>
      <div class="shift-row" style="display: grid; grid-template-columns: 2fr 2fr 2fr 1fr; gap: 12px; margin-bottom: 12px; padding: 12px; background: #f8f9fa; border-radius: 4px;">
        <select name="shifts[<?= $shiftIndex ?>][shift_type]" required>
          <?php foreach ($shiftTypes as $type): ?>
          <option value="<?= $type ?>" <?= $shift['shift_type'] === $type ? 'selected' : '' ?>>
            <?= $typeText[$type] ?>
          </option>
          <?php endforeach; ?>
        </select>
        <select name="shifts[<?= $shiftIndex ?>][employee_id]" required>
          <option value=""><?= __('list.filter') ?></option>
          <?php foreach ($employees as $emp): ?>
          <option value="<?= $emp['id'] ?>" <?= $shift['employee_id'] == $emp['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($emp['name']) ?>
          </option>
          <?php endforeach; ?>
        </select>
        <select name="shifts[<?= $shiftIndex ?>][manager_id]">
          <option value=""><?= __('shift.no_manager') ?></option>
          <?php foreach ($managers as $mgr): ?>
          <option value="<?= $mgr['id'] ?>" <?= ($shift['manager_id'] ?? '') == $mgr['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($mgr['name']) ?>
          </option>
          <?php endforeach; ?>
        </select>
        <button type="button" class="btn btn-danger" onclick="removeShiftRow(this)" style="padding: 6px 12px;">
          <?= __('btn.delete') ?>
        </button>
        <input type="hidden" name="shifts[<?= $shiftIndex ?>][note]" value="<?= htmlspecialchars($shift['note'] ?? '') ?>">
      </div>
      <?php
      $shiftIndex++;
      endforeach;
      ?>
    </div>

    <div style="margin-top: 20px;">
      <button type="submit" class="btn btn-success"><?= __('btn.submit') ?></button>
      <a href="/index.php?r=shifts/list" class="btn" style="background: #95a5a6;"><?= __('btn.cancel') ?></a>
    </div>
  </form>
</div>

<script>
let shiftIndex = <?= $shiftIndex ?>;
const shiftTypes = <?= json_encode($shiftTypes) ?>;
const typeText = <?= json_encode($typeText) ?>;
const employees = <?= json_encode($employees) ?>;
const managers = <?= json_encode($managers) ?>;

function addShiftRow() {
  const container = document.getElementById('shiftsContainer');
  const row = document.createElement('div');
  row.className = 'shift-row';
  row.style.cssText = 'display: grid; grid-template-columns: 2fr 2fr 2fr 1fr; gap: 12px; margin-bottom: 12px; padding: 12px; background: #f8f9fa; border-radius: 4px;';
  
  let typeSelect = '<select name="shifts[' + shiftIndex + '][shift_type]" required>';
  shiftTypes.forEach(type => {
    typeSelect += '<option value="' + type + '">' + typeText[type] + '</option>';
  });
  typeSelect += '</select>';
  
  let empSelect = '<select name="shifts[' + shiftIndex + '][employee_id]" required><option value=""><?= __('list.filter') ?></option>';
  employees.forEach(emp => {
    empSelect += '<option value="' + emp.id + '">' + emp.name + '</option>';
  });
  empSelect += '</select>';
  
  let mgrSelect = '<select name="shifts[' + shiftIndex + '][manager_id]"><option value=""><?= __('shift.no_manager') ?></option>';
  managers.forEach(mgr => {
    mgrSelect += '<option value="' + mgr.id + '">' + mgr.name + '</option>';
  });
  mgrSelect += '</select>';
  
  row.innerHTML = typeSelect + empSelect + mgrSelect + 
    '<button type="button" class="btn btn-danger" onclick="removeShiftRow(this)" style="padding: 6px 12px;"><?= __('btn.delete') ?></button>' +
    '<input type="hidden" name="shifts[' + shiftIndex + '][note]" value="">';
  
  container.appendChild(row);
  shiftIndex++;
}

function removeShiftRow(btn) {
  btn.closest('.shift-row').remove();
}

function clearAll() {
  if (confirm('<?= __('shift.clear_confirm') ?>')) {
    document.getElementById('shiftsContainer').innerHTML = '';
    shiftIndex = 0;
  }
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>

