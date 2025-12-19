<?php
$title = __('task.create');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('task.create') ?></h2>

<?php if ($error): ?>
<div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    
    <div class="form-group">
      <label><?= __('task.title_label') ?> *</label>
      <input type="text" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
    </div>

    <div class="form-group">
      <label><?= __('task.type') ?></label>
      <select name="type">
        <option value="temporary" <?= ($_POST['type'] ?? 'temporary') === 'temporary' ? 'selected' : '' ?>>
          <?= __('task.type_temporary') ?>
        </option>
        <option value="fixed" <?= ($_POST['type'] ?? '') === 'fixed' ? 'selected' : '' ?>>
          <?= __('task.type_fixed') ?>
        </option>
      </select>
    </div>

    <div class="form-group">
      <label><?= __('task.description') ?></label>
      <textarea name="description" rows="4"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
      <label><?= __('task.due_date') ?></label>
      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 8px;">
        <input type="date" name="due_date" value="<?= htmlspecialchars($_POST['due_date'] ?? '') ?>">
        <input type="time" name="due_time" value="<?= htmlspecialchars($_POST['due_time'] ?? '23:59:59') ?>">
      </div>
    </div>

    <div class="form-group">
      <label><?= __('task.store') ?></label>
      <select name="store">
        <option value=""><?= __('list.filter') ?></option>
        <option value="coffee" <?= ($_POST['store'] ?? '') === 'coffee' ? 'selected' : '' ?>>咖啡店</option>
        <option value="office" <?= ($_POST['store'] ?? '') === 'office' ? 'selected' : '' ?>>办公区</option>
        <option value="whiskey" <?= ($_POST['store'] ?? '') === 'whiskey' ? 'selected' : '' ?>>威士忌吧</option>
      </select>
    </div>

    <div class="form-group">
      <label><?= __('task.assign_type') ?></label>
      <select name="assign_type" id="assign_type" onchange="toggleAssignType()">
        <option value="user" <?= ($_POST['assign_type'] ?? 'user') === 'user' ? 'selected' : '' ?>>
          <?= __('task.assign_type_user') ?>
        </option>
        <option value="role" <?= ($_POST['assign_type'] ?? '') === 'role' ? 'selected' : '' ?>>
          <?= __('task.assign_type_role') ?>
        </option>
      </select>
    </div>

    <div class="form-group" id="assign_user_group">
      <label><?= __('task.assign_user') ?></label>
      <select name="assign_user_id">
        <option value=""><?= __('list.filter') ?></option>
        <?php foreach ($users as $u): ?>
        <option value="<?= $u['id'] ?>" <?= ($_POST['assign_user_id'] ?? '') == $u['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($u['display_name']) ?> (<?= htmlspecialchars($u['username']) ?>)
        </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group" id="assign_role_group" style="display: none;">
      <label><?= __('task.assign_role') ?></label>
      <select name="assign_role_id">
        <option value=""><?= __('list.filter') ?></option>
        <?php
        $lang = I18n::current();
        foreach ($roles as $r):
          $name = $lang === 'zh' ? $r['name_zh'] : $r['name_vi'];
        ?>
        <option value="<?= $r['id'] ?>" <?= ($_POST['assign_role_id'] ?? '') == $r['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($name) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label>
        <input type="checkbox" name="require_photo" value="1" <?= isset($_POST['require_photo']) ? 'checked' : '' ?>>
        <?= __('task.require_photo') ?>
      </label>
    </div>

    <div class="form-group">
      <label>
        <input type="checkbox" name="require_attachment" value="1" <?= isset($_POST['require_attachment']) ? 'checked' : '' ?>>
        <?= __('task.require_attachment') ?>
      </label>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
      <div class="form-group">
        <label><?= __('task.penalty_amount') ?></label>
        <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 8px;">
          <input type="number" name="penalty_amount" value="<?= htmlspecialchars($_POST['penalty_amount'] ?? '') ?>" 
                 step="0.01" min="0" placeholder="0">
          <select name="currency">
            <option value="VND" <?= ($_POST['currency'] ?? 'VND') === 'VND' ? 'selected' : '' ?>>VND</option>
            <option value="USD" <?= ($_POST['currency'] ?? '') === 'USD' ? 'selected' : '' ?>>USD</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label><?= __('task.reward_amount') ?></label>
        <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 8px;">
          <input type="number" name="reward_amount" value="<?= htmlspecialchars($_POST['reward_amount'] ?? '') ?>" 
                 step="0.01" min="0" placeholder="0">
          <select name="currency_reward" disabled>
            <option value="VND" selected>VND</option>
          </select>
        </div>
      </div>
    </div>

    <div style="margin-top: 20px;">
      <button type="submit" class="btn btn-success"><?= __('btn.submit') ?></button>
      <a href="/index.php?r=tasks/list" class="btn" style="background: #95a5a6;"><?= __('btn.cancel') ?></a>
    </div>
  </form>
</div>

<script>
function toggleAssignType() {
  const assignType = document.getElementById('assign_type').value;
  const userGroup = document.getElementById('assign_user_group');
  const roleGroup = document.getElementById('assign_role_group');
  
  if (assignType === 'user') {
    userGroup.style.display = 'block';
    roleGroup.style.display = 'none';
    roleGroup.querySelector('select').disabled = true;
    userGroup.querySelector('select').disabled = false;
  } else {
    userGroup.style.display = 'none';
    roleGroup.style.display = 'block';
    userGroup.querySelector('select').disabled = true;
    roleGroup.querySelector('select').disabled = false;
  }
}

// 初始化
toggleAssignType();
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>

