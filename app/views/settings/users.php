<?php
$title = __('setting.users');
include __DIR__ . '/../layout/header.php';
$lang = I18n::current();
$current = $_GET['r'] ?? '';
?>

<h2 style="margin-bottom: 20px;"><?= __('setting.users') ?></h2>

<div class="card" style="padding: 12px; margin-bottom: 16px;">
  <div style="display: flex; gap: 8px; flex-wrap: wrap;">
    <a class="btn <?= $current === 'settings/categories' ? 'btn-success' : '' ?>" href="/index.php?r=settings/categories"><?= __('setting.categories') ?></a>
    <a class="btn <?= $current === 'settings/paymentMethods' ? 'btn-success' : '' ?>" href="/index.php?r=settings/paymentMethods"><?= __('setting.payment_methods') ?></a>
    <a class="btn <?= $current === 'settings/vendors' ? 'btn-success' : '' ?>" href="/index.php?r=settings/vendors"><?= __('setting.vendors') ?></a>
    <?php if (Auth::user()['role_key'] === 'owner'): ?>
    <a class="btn <?= $current === 'settings/users' ? 'btn-success' : '' ?>" href="/index.php?r=settings/users"><?= __('setting.users') ?></a>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <h3 style="margin-bottom: 16px;"><?= __('setting.user_create') ?></h3>
  <?php if (!empty($error)): ?>
  <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="post">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    <input type="hidden" name="action" value="create">
    <div class="form-group">
      <label><?= __('user.username') ?></label>
      <input type="text" name="username" required>
    </div>
    <div class="form-group">
      <label><?= __('user.display_name') ?></label>
      <input type="text" name="display_name" required>
    </div>
    <div class="form-group">
      <label><?= __('user.password') ?></label>
      <input type="password" name="password" required>
    </div>
    <div class="form-group">
      <label><?= __('user.role') ?></label>
      <select name="role_id" required>
        <?php foreach ($roles as $role): ?>
        <option value="<?= $role['id'] ?>">
          <?= $lang === 'zh' ? $role['name_zh'] : $role['name_vi'] ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label>
        <input type="checkbox" name="is_active" checked> <?= __('user.active') ?>
      </label>
    </div>
    <div class="form-group">
      <button type="submit" class="btn btn-success"><?= __('btn.save') ?></button>
    </div>
  </form>
</div>

<div class="card">
  <h3 style="margin-bottom: 16px;"><?= __('setting.users') ?></h3>
  <table>
    <tr>
      <th>ID</th>
      <th><?= __('user.username') ?></th>
      <th><?= __('user.display_name') ?></th>
      <th><?= __('user.role') ?></th>
      <th><?= __('user.active') ?></th>
      <th><?= __('user.password') ?></th>
      <th><?= __('list.actions') ?></th>
    </tr>
    <?php foreach ($users as $u): ?>
    <tr>
      <td><?= $u['id'] ?></td>
      <td><?= htmlspecialchars($u['username']) ?></td>
      <td><?= htmlspecialchars($u['display_name']) ?></td>
      <td><?= $lang === 'zh' ? $u['role_name_zh'] : $u['role_name_vi'] ?></td>
      <td><?= $u['is_active'] ? __('user.active') : __('user.inactive') ?></td>
      <td>••••••</td>
      <td>
        <form method="post" style="display: inline-block; min-width: 260px;">
          <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?= $u['id'] ?>">
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px,1fr)); gap: 6px;">
            <input type="text" name="display_name" value="<?= htmlspecialchars($u['display_name']) ?>" placeholder="<?= __('user.display_name') ?>">
            <select name="role_id">
              <?php foreach ($roles as $role): ?>
              <option value="<?= $role['id'] ?>" <?= $u['role_id'] == $role['id'] ? 'selected' : '' ?>>
                <?= $lang === 'zh' ? $role['name_zh'] : $role['name_vi'] ?>
              </option>
              <?php endforeach; ?>
            </select>
            <input type="password" name="password" placeholder="<?= __('user.new_password') ?>">
            <label style="display: flex; align-items: center; gap: 4px; font-size: 12px;">
              <input type="checkbox" name="is_active" <?= $u['is_active'] ? 'checked' : '' ?>> <?= __('user.active') ?>
            </label>
          </div>
          <button type="submit" class="btn" style="margin-top: 6px; padding: 6px 10px; font-size: 12px;"><?= __('btn.save') ?></button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
