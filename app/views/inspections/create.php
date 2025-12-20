<?php
require_once __DIR__ . '/../../models/Inspection.php';

$title = __('nav.inspections_create', '巡店记录');
$show_back = true;
include __DIR__ . '/../layout/h5_header.php';

// 获取今日巡店次数 - 统计所有今日的巡店记录（包括pending状态），因为店长自己创建的应该立即计入
$today = date('Y-m-d');
$todayInspections = Inspection::list(['date' => $today]);
$inspectionCount = count($todayInspections);
?>

<!-- 巡店状态提示 -->
<div class="h5-card" style="background: #f0f7ff; border: 1px solid #3498db;">
  <div style="text-align: center;">
    <div style="font-size: 14px; color: #6b7280; margin-bottom: 4px;">📅 <?= __('inspection.today_inspection', '今日巡店') ?></div>
    <div style="font-size: 24px; font-weight: bold; color: #3498db;">
      <?= __('inspection.completed', '已完成') ?>: <?= $inspectionCount ?> / 24 <?= __('inspection.times', '次') ?>
    </div>
  </div>
</div>

<?php if (!empty($error)): ?>
<div class="h5-card" style="background: #fee; border: 1px solid #fcc;">
  <div style="color: #c33;"><?= htmlspecialchars($error) ?></div>
</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">

  <!-- 基本信息 -->
  <div class="h5-card">
    <div class="h5-card-title">📋 <?= __('inspection.basic_info', '基本信息') ?></div>
    
    <div class="h5-form-group">
      <label for="store"><?= __('material.store', '店面') ?> *</label>
      <select name="store" id="store" required>
        <option value="coffee" selected><?= __('asset.category_coffee', '咖啡店') ?></option>
        <option value="office"><?= __('asset.category_office', '办公室') ?></option>
        <option value="whiskey"><?= __('asset.category_whiskey', '威士忌') ?></option>
      </select>
    </div>

    <div class="h5-form-group">
      <label for="floor"><?= __('inspection.floor', '楼层') ?> *</label>
      <select name="floor" id="floor" required>
        <option value="1F" selected>1F</option>
        <option value="2F">2F</option>
        <option value="3F">3F</option>
        <option value="4F">4F</option>
      </select>
    </div>

    <div class="h5-form-group">
      <label for="visit_no"><?= __('inspection.visit_no', '巡店次数') ?> *</label>
      <select name="visit_no" id="visit_no" required>
        <option value="1" <?= ($inspectionCount + 1) == 1 ? 'selected' : '' ?>><?= __('inspection.visit_first', '首次') ?></option>
        <option value="2" <?= ($inspectionCount + 1) == 2 ? 'selected' : '' ?>><?= __('inspection.visit_second', '二次') ?></option>
      </select>
    </div>

    <div class="h5-form-group">
      <label for="room"><?= __('inspection.room', '房间/区域') ?> *</label>
      <select name="room" id="room" required>
        <option value="store" selected><?= __('inspection.room_store', '店面') ?></option>
        <option value="restroom"><?= __('inspection.room_restroom', '卫生间') ?></option>
        <option value="stair"><?= __('inspection.room_stair', '楼梯') ?></option>
      </select>
    </div>

    <div class="h5-form-group">
      <label for="status"><?= __('inspection.status', '状态') ?> *</label>
      <select name="status" id="status" required>
        <option value="ok" selected><?= __('inspection.ok', 'OK') ?></option>
        <option value="issue"><?= __('inspection.issue', '问题') ?></option>
      </select>
    </div>

    <div class="h5-form-group">
      <label for="spot_date"><?= __('field.time', '发生时间') ?> *</label>
      <input type="date" name="spot_date" id="spot_date" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" required>
      <small class="h5-hint"><?= __('inspection.no_past_date_hint', '不能选择过去的日期') ?></small>
    </div>
  </div>

  <!-- 巡店拍照区 -->
  <div class="h5-card">
    <div class="h5-card-title">📷 <?= __('inspection.photo', '巡店现场照片') ?></div>
    <div class="h5-form-group">
      <label for="photos"><?= __('inspection.photo', '巡店现场照片') ?></label>
      <input type="file" name="photos[]" id="photos" accept="image/*" multiple>
      <small class="h5-hint"><?= __('asset.photo_hint', '支持 JPG/PNG/WEBP/GIF，单张不超过 5MB，可多选上传') ?></small>
    </div>
  </div>

  <!-- 巡店说明 -->
  <div class="h5-card">
    <div class="h5-card-title">✏️ <?= __('inspection.note', '巡店说明') ?></div>
    <div class="h5-form-group">
      <label for="note"><?= __('inspection.note', '巡店说明') ?></label>
      <textarea name="note" id="note" placeholder="<?= __('inspection.note_hint', '如：吧台正常 / 后厨需注意卫生 / 高峰前检查') ?>"></textarea>
    </div>
  </div>

  <!-- 提交按钮（固定底部） -->
  <div class="fixed-bottom-btn">
    <button type="submit" class="h5-btn h5-btn-success">
      ✅ <?= __('btn.save', '保存') ?>
    </button>
  </div>
</form>

<?php include __DIR__ . '/../layout/h5_footer.php'; ?>
