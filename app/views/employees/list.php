<?php
$title = __('employee.list');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('employee.list') ?></h2>

<div class="card">
  <form method="get" style="margin-bottom: 20px;">
    <input type="hidden" name="r" value="employees/list">
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 12px;">
      <div class="form-group" style="margin-bottom: 0;">
        <label><?= __('employee.status') ?></label>
        <select name="status">
          <option value=""><?= __('list.filter') ?></option>
          <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>><?= __('employee.status_active') ?></option>
          <option value="inactive" <?= ($_GET['status'] ?? '') === 'inactive' ? 'selected' : '' ?>><?= __('employee.status_inactive') ?></option>
          <option value="resigned" <?= ($_GET['status'] ?? '') === 'resigned' ? 'selected' : '' ?>><?= __('employee.status_resigned') ?></option>
        </select>
      </div>
      
      <div class="form-group" style="margin-bottom: 0;">
        <label><?= __('employee.role') ?></label>
        <select name="role_id">
          <option value=""><?= __('list.filter') ?></option>
          <?php
          $lang = I18n::current();
          foreach ($roles as $r):
            $name = $lang === 'zh' ? $r['name_zh'] : $r['name_vi'];
          ?>
          <option value="<?= $r['id'] ?>" <?= ($_GET['role_id'] ?? '') == $r['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($name) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="form-group" style="margin-bottom: 0;">
        <label><?= __('list.search') ?></label>
        <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
               placeholder="<?= __('employee.name') ?> / <?= __('employee.phone') ?>">
      </div>
    </div>
    
    <div>
      <button type="submit" class="btn"><?= __('btn.search') ?></button>
      <a href="/index.php?r=employees/list" class="btn" style="background: #95a5a6;"><?= __('btn.cancel') ?></a>
    </div>
  </form>
</div>

<div class="card">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
    <h3><?= __('employee.list') ?></h3>
    <a href="/index.php?r=employees/create" class="btn btn-success"><?= __('employee.create') ?></a>
  </div>
  <div class="table-scroll">
    <table>
      <tr>
        <th>ID</th>
        <th><?= __('employee.name') ?></th>
        <th><?= __('employee.role') ?></th>
        <th><?= __('employee.employment_type') ?></th>
        <th><?= __('employee.phone') ?></th>
        <th><?= __('employee.email') ?></th>
        <th><?= __('employee.status') ?></th>
        <th><?= __('employee.hire_date') ?></th>
        <th><?= __('list.actions') ?></th>
      </tr>
      <?php if (empty($items)): ?>
      <tr>
        <td colspan="9" style="text-align: center; color: #999; padding: 40px;">
          <?= __('list.no_data') ?>
        </td>
      </tr>
      <?php else: ?>
      <?php
      $lang = I18n::current();
      foreach ($items as $row):
        $statusClass = [
          'active' => 'badge-income',
          'inactive' => 'badge-pending',
          'resigned' => 'badge-void'
        ];
        $statusText = [
          'active' => __('employee.status_active'),
          'inactive' => __('employee.status_inactive'),
          'resigned' => __('employee.status_resigned')
        ];
      ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($lang === 'zh' ? ($row['role_name_zh'] ?? '') : ($row['role_name_vi'] ?? '')) ?></td>
        <td>
          <span class="badge">
            <?= ($row['employment_type'] ?? 'full_time') === 'full_time' ? __('employee.employment_type_full_time') : __('employee.employment_type_part_time') ?>
          </span>
        </td>
        <td><?= htmlspecialchars($row['phone'] ?? '-') ?></td>
        <td><?= htmlspecialchars($row['email'] ?? '-') ?></td>
        <td>
          <span class="badge <?= $statusClass[$row['status']] ?? '' ?>">
            <?= $statusText[$row['status']] ?? $row['status'] ?>
          </span>
        </td>
        <td><?= $row['hire_date'] ? date('Y-m-d', strtotime($row['hire_date'])) : '-' ?></td>
        <td>
          <a href="/index.php?r=employees/view&id=<?= $row['id'] ?>" class="btn" style="padding: 4px 8px; font-size: 12px;">
            <?= __('btn.view') ?>
          </a>
          <a href="/index.php?r=employees/edit&id=<?= $row['id'] ?>" class="btn btn-success" 
             style="padding: 4px 8px; font-size: 12px;">
            <?= __('btn.edit') ?>
          </a>
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
      $queryParams['r'] = 'employees/list';
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

