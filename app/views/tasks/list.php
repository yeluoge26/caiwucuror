<?php
$title = __('task.list');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('task.list') ?></h2>

<div class="card">
  <form method="get" style="margin-bottom: 20px;">
    <input type="hidden" name="r" value="tasks/list">
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 12px;">
      <div class="form-group" style="margin-bottom: 0;">
        <label><?= __('task.status') ?></label>
        <select name="status">
          <option value=""><?= __('list.filter') ?></option>
          <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>><?= __('task.status_pending') ?></option>
          <option value="in_progress" <?= ($_GET['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>><?= __('task.status_in_progress') ?></option>
          <option value="completed" <?= ($_GET['status'] ?? '') === 'completed' ? 'selected' : '' ?>><?= __('task.status_completed') ?></option>
          <option value="approved" <?= ($_GET['status'] ?? '') === 'approved' ? 'selected' : '' ?>><?= __('task.status_approved') ?></option>
        </select>
      </div>
      
      <div class="form-group" style="margin-bottom: 0;">
        <label><?= __('task.type') ?></label>
        <select name="type">
          <option value=""><?= __('list.filter') ?></option>
          <option value="fixed" <?= ($_GET['type'] ?? '') === 'fixed' ? 'selected' : '' ?>><?= __('task.type_fixed') ?></option>
          <option value="temporary" <?= ($_GET['type'] ?? '') === 'temporary' ? 'selected' : '' ?>><?= __('task.type_temporary') ?></option>
        </select>
      </div>
      
      <div class="form-group" style="margin-bottom: 0;">
        <label><?= __('list.search') ?></label>
        <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
               placeholder="<?= __('task.title_label') ?>">
      </div>
    </div>
    
    <div>
      <button type="submit" class="btn"><?= __('btn.search') ?></button>
      <a href="/index.php?r=tasks/list" class="btn" style="background: #95a5a6;"><?= __('btn.cancel') ?></a>
    </div>
  </form>
</div>

<div class="card">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
    <h3><?= __('task.list') ?></h3>
    <?php if (in_array($user['role_key'], ['owner', 'manager'])): ?>
    <a href="/index.php?r=tasks/create" class="btn btn-success"><?= __('task.create') ?></a>
    <?php endif; ?>
  </div>
  <div class="table-scroll">
    <table>
      <tr>
        <th>ID</th>
        <th><?= __('task.title_label') ?></th>
        <th><?= __('task.type') ?></th>
        <th><?= __('task.status') ?></th>
        <th><?= __('task.assign_user') ?></th>
        <th><?= __('task.due_date') ?></th>
        <th><?= __('task.created_by') ?></th>
        <th><?= __('list.actions') ?></th>
      </tr>
      <?php if (empty($items)): ?>
      <tr>
        <td colspan="8" style="text-align: center; color: #999; padding: 40px;">
          <?= __('list.no_data') ?>
        </td>
      </tr>
      <?php else: ?>
      <?php
      $lang = I18n::current();
      foreach ($items as $row):
        $statusClass = [
          'pending' => 'badge-pending',
          'in_progress' => 'badge-approved',
          'completed' => 'badge-income',
          'approved' => 'badge-success',
          'rejected' => 'badge-void'
        ];
        $statusText = [
          'pending' => __('task.status_pending'),
          'in_progress' => __('task.status_in_progress'),
          'completed' => __('task.status_completed'),
          'approved' => __('task.status_approved'),
          'rejected' => __('task.status_rejected')
        ];
      ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['title']) ?></td>
        <td>
          <span class="badge">
            <?= $row['type'] === 'fixed' ? __('task.type_fixed') : __('task.type_temporary') ?>
          </span>
        </td>
        <td>
          <span class="badge <?= $statusClass[$row['status']] ?? '' ?>">
            <?= $statusText[$row['status']] ?? $row['status'] ?>
          </span>
        </td>
        <td><?= htmlspecialchars($row['assign_user_name'] ?? '-') ?></td>
        <td><?= $row['due_date'] ? date('Y-m-d H:i', strtotime($row['due_date'])) : '-' ?></td>
        <td><?= htmlspecialchars($row['creator_name'] ?? '') ?></td>
        <td>
          <a href="/index.php?r=tasks/view&id=<?= $row['id'] ?>" class="btn" style="padding: 4px 8px; font-size: 12px;">
            <?= __('task.view') ?>
          </a>
          <?php if ($row['assign_user_id'] == $user['id'] && $row['status'] === 'pending'): ?>
          <a href="/index.php?r=tasks/start&id=<?= $row['id'] ?>" class="btn btn-success" 
             style="padding: 4px 8px; font-size: 12px;">
            <?= __('task.start') ?>
          </a>
          <?php endif; ?>
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
      $queryParams['r'] = 'tasks/list';
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

