<?php
$title = __('shift.list');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('shift.list') ?></h2>

<div class="card">
  <form method="get" style="margin-bottom: 20px;">
    <input type="hidden" name="r" value="shifts/list">
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 12px;">
      <div class="form-group" style="margin-bottom: 0;">
        <label><?= __('shift.shift_date') ?></label>
        <input type="date" name="shift_date" value="<?= htmlspecialchars($_GET['shift_date'] ?? '') ?>">
      </div>
      
      <div class="form-group" style="margin-bottom: 0;">
        <label><?= __('shift.from_date') ?></label>
        <input type="date" name="from_date" value="<?= htmlspecialchars($_GET['from_date'] ?? '') ?>">
      </div>
      
      <div class="form-group" style="margin-bottom: 0;">
        <label><?= __('shift.to_date') ?></label>
        <input type="date" name="to_date" value="<?= htmlspecialchars($_GET['to_date'] ?? '') ?>">
      </div>
      
      <div class="form-group" style="margin-bottom: 0;">
        <label><?= __('shift.shift_type') ?></label>
        <select name="shift_type">
          <option value=""><?= __('list.filter') ?></option>
          <option value="morning" <?= ($_GET['shift_type'] ?? '') === 'morning' ? 'selected' : '' ?>><?= __('shift.type_morning') ?></option>
          <option value="afternoon" <?= ($_GET['shift_type'] ?? '') === 'afternoon' ? 'selected' : '' ?>><?= __('shift.type_afternoon') ?></option>
          <option value="evening" <?= ($_GET['shift_type'] ?? '') === 'evening' ? 'selected' : '' ?>><?= __('shift.type_evening') ?></option>
        </select>
      </div>
      
      <div class="form-group" style="margin-bottom: 0;">
        <label><?= __('shift.is_confirmed') ?></label>
        <select name="is_confirmed">
          <option value=""><?= __('list.filter') ?></option>
          <option value="1" <?= ($_GET['is_confirmed'] ?? '') === '1' ? 'selected' : '' ?>><?= __('shift.confirmed') ?></option>
          <option value="0" <?= ($_GET['is_confirmed'] ?? '') === '0' ? 'selected' : '' ?>><?= __('shift.not_confirmed') ?></option>
        </select>
      </div>
    </div>
    
    <div>
      <button type="submit" class="btn"><?= __('btn.search') ?></button>
      <a href="/index.php?r=shifts/list" class="btn" style="background: #95a5a6;"><?= __('btn.cancel') ?></a>
    </div>
  </form>
</div>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success">
  <?php
  $lang = I18n::current();
  $success = intval($_GET['success'] ?? 0);
  $failed = intval($_GET['failed'] ?? 0);
  if ($lang === 'zh') {
    echo "排班完成：成功 {$success} 条，失败 {$failed} 条";
  } else {
    echo "Lập lịch hoàn tất: Thành công {$success} bản ghi, Thất bại {$failed} bản ghi";
  }
  ?>
</div>
<?php endif; ?>

<div class="card">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 8px;">
    <h3><?= __('shift.list') ?></h3>
    <div style="display: flex; gap: 8px;">
      <a href="/index.php?r=shifts/create" class="btn btn-success"><?= __('shift.create') ?></a>
      <a href="/index.php?r=shifts/schedule" class="btn"><?= __('shift.schedule') ?></a>
    </div>
  </div>
  <div class="table-scroll">
    <table>
      <tr>
        <th>ID</th>
        <th><?= __('shift.shift_date') ?></th>
        <th><?= __('shift.shift_type') ?></th>
        <th><?= __('shift.employee') ?></th>
        <th><?= __('shift.manager') ?></th>
        <th><?= __('shift.is_confirmed') ?></th>
        <th><?= __('list.actions') ?></th>
      </tr>
      <?php if (empty($items)): ?>
      <tr>
        <td colspan="7" style="text-align: center; color: #999; padding: 40px;">
          <?= __('list.no_data') ?>
        </td>
      </tr>
      <?php else: ?>
      <?php
      $lang = I18n::current();
      foreach ($items as $row):
        $typeText = [
          'morning' => __('shift.type_morning'),
          'afternoon' => __('shift.type_afternoon'),
          'evening' => __('shift.type_evening')
        ];
      ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= date('Y-m-d', strtotime($row['shift_date'])) ?></td>
        <td><?= $typeText[$row['shift_type']] ?? $row['shift_type'] ?></td>
        <td><?= htmlspecialchars($row['employee_name'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['manager_name'] ?? '-') ?></td>
        <td>
          <?php if ($row['is_confirmed']): ?>
          <span class="badge badge-income"><?= __('shift.confirmed') ?></span>
          <?php else: ?>
          <span class="badge badge-pending"><?= __('shift.not_confirmed') ?></span>
          <?php endif; ?>
        </td>
        <td>
          <div style="display: flex; gap: 4px; flex-wrap: wrap;">
            <a href="/index.php?r=shifts/edit&id=<?= $row['id'] ?>" 
               class="btn" style="padding: 4px 8px; font-size: 12px;">
              <?= __('btn.edit') ?>
            </a>
            <?php if (!$row['is_confirmed']): ?>
            <a href="/index.php?r=shifts/quickConfirm&id=<?= $row['id'] ?>&confirmed=1" 
               class="btn btn-success" style="padding: 4px 8px; font-size: 12px;"
               onclick="return confirm('<?= __('shift.confirm_confirm') ?>')">
              <?= __('shift.confirm') ?>
            </a>
            <?php else: ?>
            <a href="/index.php?r=shifts/quickConfirm&id=<?= $row['id'] ?>&confirmed=0" 
               class="btn" style="padding: 4px 8px; font-size: 12px;">
              <?= __('shift.cancel_confirm') ?>
            </a>
            <?php endif; ?>
            <?php if ($row['manager_id']): ?>
            <a href="/index.php?r=shifts/removeManager&id=<?= $row['id'] ?>" 
               class="btn" style="padding: 4px 8px; font-size: 12px; background: #95a5a6;"
               onclick="return confirm('<?= __('shift.remove_manager_confirm') ?>')"
               title="<?= __('shift.remove_manager') ?>">
              <?= __('shift.remove_manager') ?>
            </a>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
    </table>
  </div>

  <?php if (isset($totalPages) && $totalPages > 1): ?>
  <div style="margin-top: 20px; display: flex; justify-content: center; align-items: center; gap: 8px; flex-wrap: wrap;">
    <?php
    $currentPage = $page ?? 1;
    $queryParams = $_GET;
    
    $buildPageUrl = function($pageNum) use ($queryParams) {
      $queryParams['page'] = $pageNum;
      $queryParams['r'] = 'shifts/list';
      return '/index.php?' . http_build_query($queryParams);
    };
    
    $lang = I18n::current();
    ?>
    
    <?php if ($currentPage > 1): ?>
      <a href="<?= htmlspecialchars($buildPageUrl(1)) ?>" class="btn" style="padding: 6px 12px;">
        <?= $lang === 'zh' ? '首页' : 'Đầu' ?>
      </a>
      <a href="<?= htmlspecialchars($buildPageUrl($currentPage - 1)) ?>" class="btn" style="padding: 6px 12px;">
        <?= $lang === 'zh' ? '上一页' : 'Trước' ?>
      </a>
    <?php endif; ?>
    
    <span style="padding: 6px 12px;">
      <?php
      if ($lang === 'zh') {
        echo "第 {$currentPage} 页 / 共 {$totalPages} 页 (共 {$total} 条)";
      } else {
        echo "Trang {$currentPage} / {$totalPages} (Tổng {$total} bản ghi)";
      }
      ?>
    </span>
    
    <?php if ($currentPage < $totalPages): ?>
      <a href="<?= htmlspecialchars($buildPageUrl($currentPage + 1)) ?>" class="btn" style="padding: 6px 12px;">
        <?= $lang === 'zh' ? '下一页' : 'Sau' ?>
      </a>
      <a href="<?= htmlspecialchars($buildPageUrl($totalPages)) ?>" class="btn" style="padding: 6px 12px;">
        <?= $lang === 'zh' ? '末页' : 'Cuối' ?>
      </a>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

