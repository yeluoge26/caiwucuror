<?php
require_once __DIR__ . '/../../models/Inspection.php';

$title = __('nav.inspections_create', '巡店记录');
$show_back = true;
include __DIR__ . '/../layout/h5_header.php';

// 获取今日巡店次数
$today = date('Y-m-d');
$todayInspections = Inspection::list(['date' => $today]);
$confirmedInspections = array_filter($todayInspections, function($item) {
  return $item['reviewed_status'] === 'confirmed';
});
$inspectionCount = count($confirmedInspections);
?>

<!-- 巡店状态提示 -->
<div class="h5-card" style="background: #f0f7ff; border: 1px solid #3498db;">
  <div style="text-align: center;">
    <div style="font-size: 14px; color: #6b7280; margin-bottom: 4px;">📅 <?= __('inspection.today_inspection', '今日巡店') ?></div>
    <div style="font-size: 24px; font-weight: bold; color: #3498db;">
      <?= __('inspection.completed', '已完成') ?>: <?= $inspectionCount ?> / 2 <?= __('inspection.times', '次') ?>
    </div>
  </div>
</div>

<form method="post" enctype="multipart/form-data" id="inspection-form">
  <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
  <input type="hidden" name="spot_date" value="<?= date('Y-m-d') ?>">
  <input type="hidden" name="store" value="coffee">
  <input type="hidden" name="floor" value="1F">
  <input type="hidden" name="visit_no" value="<?= $inspectionCount + 1 ?>">
  <input type="hidden" name="room" value="store">
  <input type="hidden" name="status" value="ok">

  <!-- 巡店拍照区（核心，必须完成） -->
  <div class="h5-card">
    <div class="h5-card-title">📷 <?= __('inspection.photo', '巡店现场照片') ?>（<?= __('field.required', '必填') ?>）</div>
    
    <div class="photo-upload-area" id="photo-area">
      <input type="file" name="photos[]" id="photo-input" accept="image/*" capture="environment" multiple required style="display: none;">
      <button type="button" onclick="document.getElementById('photo-input').click()" class="h5-btn" style="background: #3498db;">
        📸 <?= __('inspection.take_photo', '拍照') ?>
      </button>
      <div class="h5-hint required" style="margin-top: 8px;">
        <?= __('inspection.photo_required_hint', '只能调用相机，禁止相册上传。至少 1 张，建议 ≤5 张') ?>
      </div>
      <div class="photo-preview" id="photo-preview"></div>
    </div>
  </div>

  <!-- 巡店说明（可选） -->
  <div class="h5-card">
    <div class="h5-card-title">✏️ <?= __('inspection.note', '巡店说明') ?>（<?= __('field.optional', '可选') ?>）</div>
    <div class="h5-form-group">
      <textarea name="note" placeholder="<?= __('inspection.note_hint', '如：吧台正常 / 后厨需注意卫生 / 高峰前检查') ?>"></textarea>
    </div>
  </div>

  <!-- 发现问题？（快捷入口） -->
  <div class="h5-card">
    <div class="h5-card-title">⚠ <?= __('inspection.found_issue', '是否发现问题？') ?></div>
    <div style="display: flex; gap: 12px;">
      <label style="flex: 1; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; text-align: center; cursor: pointer;">
        <input type="radio" name="has_issue" value="no" checked style="margin-right: 8px;">
        <?= __('inspection.no_issue', '无问题') ?>
      </label>
      <a href="/index.php?r=tasks/create?type=issue&inspection_id=current" style="flex: 1; padding: 12px; border: 2px solid #3498db; border-radius: 8px; text-align: center; text-decoration: none; color: #3498db; font-weight: 600;">
        ➕ <?= __('inspection.record_issue', '记录问题') ?>
      </a>
    </div>
  </div>

  <!-- 提交按钮（固定底部） -->
  <div class="fixed-bottom-btn">
    <button type="submit" class="h5-btn h5-btn-success" id="submit-btn" disabled>
      ✅ <?= __('inspection.submit', '提交巡店') ?>
    </button>
  </div>
</form>

<script>
const photoInput = document.getElementById('photo-input');
const photoPreview = document.getElementById('photo-preview');
const submitBtn = document.getElementById('submit-btn');
const photoArea = document.getElementById('photo-area');
let selectedPhotos = [];

photoInput.addEventListener('change', function(e) {
  const files = Array.from(e.target.files);
  if (files.length === 0) return;
  
  // 限制最多5张
  if (selectedPhotos.length + files.length > 5) {
    alert('<?= __('inspection.max_photos', '最多只能上传5张照片') ?>');
    return;
  }
  
  files.forEach(file => {
    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = function(e) {
        selectedPhotos.push({
          file: file,
          preview: e.target.result
        });
        updatePhotoPreview();
        updateSubmitButton();
      };
      reader.readAsDataURL(file);
    }
  });
  
  // 重置input以便可以再次选择同一文件
  photoInput.value = '';
});

function updatePhotoPreview() {
  photoPreview.innerHTML = '';
  
  selectedPhotos.forEach((photo, index) => {
    const div = document.createElement('div');
    div.className = 'photo-item';
    div.innerHTML = `
      <img src="${photo.preview}" alt="Photo ${index + 1}">
      <button type="button" class="remove" onclick="removePhoto(${index})">×</button>
    `;
    photoPreview.appendChild(div);
  });
  
  // 如果还有空间，显示添加按钮
  if (selectedPhotos.length < 5) {
    const addBtn = document.createElement('div');
    addBtn.className = 'photo-item';
    addBtn.style.display = 'flex';
    addBtn.style.alignItems = 'center';
    addBtn.style.justifyContent = 'center';
    addBtn.style.cursor = 'pointer';
    addBtn.style.border = '2px dashed #d1d5db';
    addBtn.innerHTML = '<span style="font-size: 24px; color: #9ca3af;">+</span>';
    addBtn.onclick = () => photoInput.click();
    photoPreview.appendChild(addBtn);
  }
  
  if (selectedPhotos.length > 0) {
    photoArea.classList.add('has-photos');
  } else {
    photoArea.classList.remove('has-photos');
  }
}

function removePhoto(index) {
  selectedPhotos.splice(index, 1);
  updatePhotoPreview();
  updateSubmitButton();
  
  // 更新文件input
  const dt = new DataTransfer();
  selectedPhotos.forEach(photo => dt.items.add(photo.file));
  photoInput.files = dt.files;
}

function updateSubmitButton() {
  if (selectedPhotos.length > 0) {
    submitBtn.disabled = false;
  } else {
    submitBtn.disabled = true;
  }
}

// 表单提交前验证
document.getElementById('inspection-form').addEventListener('submit', function(e) {
  if (selectedPhotos.length === 0) {
    e.preventDefault();
    alert('<?= __('inspection.photo_required', '请至少拍摄1张照片') ?>');
    return false;
  }
  
  // 确保文件被包含在表单中
  const dt = new DataTransfer();
  selectedPhotos.forEach(photo => dt.items.add(photo.file));
  photoInput.files = dt.files;
});
</script>

<?php include __DIR__ . '/../layout/h5_footer.php'; ?>
