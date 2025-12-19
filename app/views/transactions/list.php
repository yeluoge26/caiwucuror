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
    </div>
    
    <div>
      <button type="submit" class="btn"><?= __('btn.search') ?></button>
      <a href="/index.php?r=transactions/list" class="btn" style="background: #95a5a6;"><?= __('btn.cancel') ?></a>
    </div>
  </form>
</div>

<?php 
$canManage = in_array($user['role_key'], ['owner', 'manager', 'accountant']); 
if (isset($_GET['import'])): 
?>
  <div class="alert <?= ($_GET['failed'] ?? 0) > 0 ? 'alert-error' : 'alert-success' ?>" style="margin-bottom: 12px;">
    <?php
    $lang = I18n::current();
    $success = intval($_GET['success'] ?? 0);
    $failed = intval($_GET['failed'] ?? 0);
    if ($lang === 'zh') {
      echo "导入完成：成功 {$success} 条，失败 {$failed} 条";
    } else {
      echo "Nhập hoàn tất: Thành công {$success} bản ghi, Thất bại {$failed} bản ghi";
    }
    ?>
  </div>
<?php endif; ?>

<div class="card">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 8px;">
    <h3><?= __('nav.list') ?></h3>
    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
      <a href="/index.php?r=transactions/create" class="btn btn-success"><?= __('nav.add') ?></a>
      <?php if ($canManage): ?>
      <?php
      // 构建导出URL，保留所有筛选条件
      $exportParams = ['r' => 'transactions/export'];
      foreach (['type', 'from', 'to', 'category_id', 'payment_method_id', 'created_by', 'search'] as $key) {
        if (!empty($_GET[$key])) {
          $exportParams[$key] = $_GET[$key];
        }
      }
      $exportUrl = '/index.php?' . http_build_query($exportParams);
      ?>
      <a href="<?= htmlspecialchars($exportUrl) ?>" class="btn">
        <?php
        $lang = I18n::current();
        echo $lang === 'zh' ? '导出' : 'Xuất';
        ?>
      </a>
      <form method="post" action="/index.php?r=transactions/import" enctype="multipart/form-data" style="display: inline-flex; gap: 8px; align-items: center;">
        <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
        <input type="file" name="transaction_csv" accept=".csv" required style="font-size: 12px;">
        <button type="submit" class="btn">
          <?php
          $lang = I18n::current();
          echo $lang === 'zh' ? '导入' : 'Nhập';
          ?>
        </button>
        <a href="/app/views/transactions/import_example.csv" download class="btn" style="background: #95a5a6; font-size: 12px;" title="<?= $lang === 'zh' ? '下载示例文件' : 'Tải file mẫu' ?>">
          <?= $lang === 'zh' ? '示例' : 'Mẫu' ?>
        </a>
      </form>
      <?php endif; ?>
    </div>
  </div>
  <div class="table-scroll">
    <table>
      <tr>
        <th>ID</th>
        <th><?= __('tx.type') ?></th>
        <th><?= __('tx.amount') ?></th>
        <th><?= __('tx.category') ?></th>
        <th><?= __('field.payment') ?></th>
        <th><?= __('field.vendor') ?></th>
        <th><?= __('field.payer') ?></th>
        <th><?= __('field.time') ?></th>
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
        <td><?= htmlspecialchars($row['payer'] ?? '-') ?></td>
        <td><?= date('d/m/Y H:i', strtotime($row['occurred_at'])) ?></td>
        <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
          <?= htmlspecialchars($row['note'] ?? '') ?>
        </td>
        <td>
          <a href="/index.php?r=transactions/view&id=<?= $row['id'] ?>" class="btn" style="padding: 4px 8px; font-size: 12px;">
            <?= __('tx.view') ?>
          </a>
          <?php if (in_array($user['role_key'], ['owner', 'accountant']) && $row['status'] !== 'void'): ?>
          <a href="/index.php?r=transactions/void&id=<?= $row['id'] ?>" class="btn btn-danger" 
             style="padding: 4px 8px; font-size: 12px;" 
             onclick="return confirm('<?= __('tx.void_confirm') ?>')">
            <?= __('btn.void') ?>
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
    
    // 构建分页链接
    $buildPageUrl = function($pageNum) use ($queryParams) {
      $queryParams['page'] = $pageNum;
      $queryParams['r'] = 'transactions/list';
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
