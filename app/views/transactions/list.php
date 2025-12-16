<?php
$title = __('nav.list');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('nav.list') ?></h2>

<div class="card">
  <form method="get" style="margin-bottom: 20px;">
    <input type="hidden" name="r" value="transactions/list">
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 12px;">
      <div class="form-group" style="margin-bottom: 0;">
        <label><?= __('tx.type') ?></label>
        <select name="type">
          <option value=""><?= __('list.filter') ?></option>
          <option value="income" <?= ($_GET['type'] ?? '') === 'income' ? 'selected' : '' ?>><?= __('tx.income') ?></option>
          <option value="expense" <?= ($_GET['type'] ?? '') === 'expense' ? 'selected' : '' ?>><?= __('tx.expense') ?></option>
        </select>
      </div>
      
      <div class="form-group" style="margin-bottom: 0;">
        <label><?= __('field.category') ?></label>
        <select name="category_id">
          <option value=""><?= __('list.filter') ?></option>
          <?php
          $lang = I18n::current();
          foreach ($categories as $cat):
            $name = $lang === 'zh' ? $cat['name_zh'] : $cat['name_vi'];
          ?>
          <option value="<?= $cat['id'] ?>" <?= ($_GET['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($name) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="form-group" style="margin-bottom: 0;">
        <label><?= __('field.payment') ?></label>
        <select name="payment_method_id">
          <option value=""><?= __('list.filter') ?></option>
          <?php
          foreach ($paymentMethods as $pm):
            $name = $lang === 'zh' ? $pm['name_zh'] : $pm['name_vi'];
          ?>
          <option value="<?= $pm['id'] ?>" <?= ($_GET['payment_method_id'] ?? '') == $pm['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($name) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="form-group" style="margin-bottom: 0;">
        <label><?= __('field.vendor') ?></label>
        <select name="vendor_id">
          <option value=""><?= __('list.filter') ?></option>
          <?php foreach ($vendors as $vendor): ?>
          <option value="<?= $vendor['id'] ?>" <?= ($_GET['vendor_id'] ?? '') == $vendor['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($vendor['name']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="form-group" style="margin-bottom: 0;">
        <label><?= __('field.time') ?> (<?= __('list.filter') ?>)</label>
        <input type="date" name="from" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>">
      </div>
      
      <div class="form-group" style="margin-bottom: 0;">
        <label>&nbsp;</label>
        <input type="date" name="to" value="<?= htmlspecialchars($_GET['to'] ?? '') ?>">
      </div>
      
      <div class="form-group" style="margin-bottom: 0;">
        <label><?= __('list.search') ?></label>
        <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
               placeholder="<?= __('field.note') ?> / <?= __('field.vendor') ?>">
      </div>
      <?php if ($user['role_key'] === 'owner'): ?>
      <div class="form-group" style="margin-bottom: 0;">
        <label><?= __('tx.status') ?></label>
        <select name="status">
          <option value=""><?= __('list.filter') ?></option>
          <option value="approved" <?= ($_GET['status'] ?? '') === 'approved' ? 'selected' : '' ?>><?= __('tx.approved') ?></option>
          <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>><?= __('tx.pending') ?></option>
        </select>
      </div>
      <?php endif; ?>
    </div>
    
    <div>
      <button type="submit" class="btn"><?= __('btn.search') ?></button>
      <a href="/index.php?r=transactions/list" class="btn" style="background: #95a5a6;"><?= __('btn.cancel') ?></a>
      <?php if ($user['role_key'] === 'owner'): ?>
      <a href="/index.php?r=transactions/list&show_pending=1" class="btn" style="background: #e67e22;">
        <?= __('list.show_pending') ?>
      </a>
      <?php endif; ?>
    </div>
  </form>
</div>

<div class="card">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
    <h3><?= __('nav.list') ?></h3>
    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
      <a href="/index.php?r=transactions/create" class="btn btn-success"><?= __('nav.add') ?></a>
      <a href="/index.php?r=export/excel<?= !empty($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : '' ?>" class="btn" style="background: #27ae60;">
        <?= __('btn.export') ?> Excel
      </a>
      <a href="/index.php?r=export/csv<?= !empty($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : '' ?>" class="btn" style="background: #16a085;">
        <?= __('btn.export') ?> CSV
      </a>
      <?php if (in_array($user['role_key'], ['owner', 'manager', 'accountant'])): ?>
      <a href="/index.php?r=import/index" class="btn" style="background: #8e44ad;">
        <?= __('btn.import') ?>
      </a>
      <?php endif; ?>
    </div>
  </div>
  
  <table>
    <tr>
      <th>ID</th>
      <th><?= __('tx.type') ?></th>
      <th><?= __('tx.amount') ?></th>
      <th><?= __('tx.category') ?></th>
      <th><?= __('field.payment') ?></th>
      <th><?= __('field.vendor') ?></th>
      <th><?= __('field.time') ?></th>
      <th><?= __('tx.status') ?></th>
      <th><?= __('field.note') ?></th>
      <th><?= __('list.actions') ?></th>
    </tr>
    <?php if (empty($items)): ?>
    <tr>
      <td colspan="10" style="text-align: center; color: #999; padding: 40px;">
        <?= __('list.no_data') ?>
      </td>
    </tr>
    <?php else: ?>
    <?php
    $lang = I18n::current();
    foreach ($items as $row):
    ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td>
        <span class="badge <?= $row['type'] === 'income' ? 'badge-income' : 'badge-expense' ?>">
          <?= $row['type'] === 'income' ? __('tx.income') : __('tx.expense') ?>
        </span>
      </td>
      <td style="font-weight: bold;">
        <?= number_format($row['amount'], 0, ',', '.') ?> <?= $row['currency'] ?>
      </td>
      <td>
        <?= htmlspecialchars($lang === 'zh' ? ($row['category_name_zh'] ?? '') : ($row['category_name_vi'] ?? '')) ?>
      </td>
      <td>
        <?= htmlspecialchars($lang === 'zh' ? ($row['payment_name_zh'] ?? '') : ($row['payment_name_vi'] ?? '')) ?>
      </td>
      <td><?= htmlspecialchars($row['vendor_name'] ?? '-') ?></td>
      <td><?= date('d/m/Y H:i', strtotime($row['occurred_at'])) ?></td>
      <td>
        <span class="badge badge-<?= $row['status'] ?>">
          <?php
          if ($row['status'] === 'approved') echo __('tx.approved');
          elseif ($row['status'] === 'pending') echo __('tx.pending');
          else echo __('tx.void');
          ?>
        </span>
      </td>
      <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
        <?= htmlspecialchars($row['note'] ?? '') ?>
      </td>
      <td>
        <div style="display: flex; gap: 4px; flex-wrap: wrap;">
          <a href="/index.php?r=transactions/view&id=<?= $row['id'] ?>" class="btn" style="padding: 4px 8px; font-size: 12px;">
            <?= __('tx.view') ?>
          </a>
          <?php if (in_array($user['role_key'], ['owner', 'manager', 'accountant']) && $row['status'] === 'pending'): ?>
          <a href="/index.php?r=transactions/edit&id=<?= $row['id'] ?>" class="btn" style="padding: 4px 8px; font-size: 12px; background: #f39c12;">
            <?= __('btn.edit') ?>
          </a>
          <?php endif; ?>
          <?php if ($user['role_key'] === 'owner' && $row['status'] === 'pending'): ?>
          <a href="/index.php?r=transactions/approve&id=<?= $row['id'] ?>" class="btn btn-success" style="padding: 4px 8px; font-size: 12px;">
            <?= __('tx.approve') ?>
          </a>
          <?php endif; ?>
          <?php if (in_array($user['role_key'], ['owner', 'accountant']) && $row['status'] !== 'void'): ?>
          <a href="/index.php?r=transactions/void&id=<?= $row['id'] ?>" class="btn btn-danger" 
             style="padding: 4px 8px; font-size: 12px;" 
             onclick="return confirm('<?= __('tx.void_confirm') ?>')">
            <?= __('btn.void') ?>
          </a>
          <?php endif; ?>
        </div>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
  </table>
  
  <!-- 分页 -->
  <?php if ($totalPages > 1): ?>
  <div style="margin-top: 20px; display: flex; justify-content: center; align-items: center; gap: 8px; flex-wrap: wrap;">
    <?php
    $queryParams = $_GET;
    unset($queryParams['page']);
    $queryString = http_build_query($queryParams);
    $baseUrl = '/index.php?r=transactions/list' . ($queryString ? '&' . $queryString : '');
    ?>
    <?php if ($page > 1): ?>
    <a href="<?= $baseUrl ?>&page=<?= $page - 1 ?>" class="btn" style="padding: 6px 12px;">
      « <?= __('list.prev') ?>
    </a>
    <?php endif; ?>
    
    <span style="padding: 6px 12px;">
      <?= __('list.page') ?> <?= $page ?> / <?= $totalPages ?> 
      (<?= __('list.total') ?>: <?= $total ?>)
    </span>
    
    <?php if ($page < $totalPages): ?>
    <a href="<?= $baseUrl ?>&page=<?= $page + 1 ?>" class="btn" style="padding: 6px 12px;">
      <?= __('list.next') ?> »
    </a>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

