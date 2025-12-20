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

  <!-- 基本信息 -->
  <div class="h5-card">
    <div class="h5-card-title">📋 <?= __('inspection.basic_info', '基本信息') ?></div>
    
    <div class="h5-form-group">
      <label><?= __('material.store', '店面') ?> *</label>
      <select name="store" required>
        <option value="coffee" selected><?= __('asset.category_coffee', '咖啡店') ?></option>
        <option value="office"><?= __('asset.category_office', '办公室') ?></option>
        <option value="whiskey"><?= __('asset.category_whiskey', '威士忌') ?></option>
      </select>
    </div>

    <div class="h5-form-group">
      <label><?= __('inspection.floor', '楼层') ?> *</label>
      <select name="floor" required>
        <option value="1F" selected>1F</option>
        <option value="2F">2F</option>
        <option value="3F">3F</option>
        <option value="4F">4F</option>
      </select>
    </div>

    <div class="h5-form-group">
      <label><?= __('inspection.visit_no', '巡店次数') ?> *</label>
      <select name="visit_no" required>
        <option value="1" <?= ($inspectionCount + 1) == 1 ? 'selected' : '' ?>><?= __('inspection.visit_first', '首次') ?></option>
        <option value="2" <?= ($inspectionCount + 1) == 2 ? 'selected' : '' ?>><?= __('inspection.visit_second', '二次') ?></option>
      </select>
    </div>

    <div class="h5-form-group">
      <label><?= __('inspection.room', '房间/区域') ?> *</label>
      <select name="room" required>
        <option value="store" selected><?= __('inspection.room_store', '店面') ?></option>
        <option value="restroom"><?= __('inspection.room_restroom', '卫生间') ?></option>
        <option value="stair"><?= __('inspection.room_stair', '楼梯') ?></option>
      </select>
    </div>

    <div class="h5-form-group">
      <label><?= __('inspection.status', '状态') ?> *</label>
      <select name="status" required>
        <option value="ok" selected><?= __('inspection.ok', 'OK') ?></option>
        <option value="issue"><?= __('inspection.issue', '问题') ?></option>
      </select>
    </div>

    <div class="h5-form-group">
      <label><?= __('field.time', '发生时间') ?> *</label>
      <input type="date" name="spot_date" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" required>
      <small class="h5-hint"><?= __('inspection.no_past_date_hint', '不能选择过去的日期') ?></small>
    </div>
  </div>

  <!-- 巡店拍照区（核心，必须完成） -->
  <div class="h5-card">
    <div class="h5-card-title">📷 <?= __('inspection.photo', '巡店现场照片') ?>（<?= __('field.required', '必填') ?>）</div>
    
    <div class="photo-upload-area" id="photo-area">
      <input type="file" name="photos[]" id="photo-input" accept="image/*" capture="environment" multiple required style="position: absolute; width: 0; height: 0; opacity: 0; overflow: hidden;">
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

  <!-- 提交按钮（固定底部） -->
  <div class="fixed-bottom-btn">
    <button type="submit" class="h5-btn h5-btn-success" id="submit-btn" disabled>
      ✅ <?= __('inspection.submit', '提交巡店') ?>
    </button>
  </div>
  
  <!-- 隐藏的原生文件输入，用于传统表单提交 -->
  <input type="file" name="photos[]" id="hidden-photo-input" accept="image/*" multiple style="display: none;">
</form>

<script>
// 等待 DOM 加载完成
document.addEventListener('DOMContentLoaded', function() {
  const photoInput = document.getElementById('photo-input');
  const photoPreview = document.getElementById('photo-preview');
  const submitBtn = document.getElementById('submit-btn');
  const photoArea = document.getElementById('photo-area');
  const form = document.getElementById('inspection-form');
  let selectedFiles = [];

  if (!photoInput || !submitBtn || !form) {
    console.error('Required elements not found');
    return;
  }

  photoInput.addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    if (files.length === 0) return;
    
    // 限制最多5张
    if (selectedFiles.length + files.length > 5) {
      alert('<?= __('inspection.max_photos', '最多只能上传5张照片') ?>');
      photoInput.value = '';
      return;
    }
    
    // 添加新文件到数组
    files.forEach(file => {
      if (file.type.startsWith('image/')) {
        selectedFiles.push(file);
      }
    });
    
    updatePhotoPreview();
    updateSubmitButton();
    
    // 重置input以便可以再次选择同一文件
    photoInput.value = '';
  });

  function updatePhotoPreview() {
  photoPreview.innerHTML = '';
  
  selectedFiles.forEach((file, index) => {
    const div = document.createElement('div');
    div.className = 'photo-item';
    
    // 创建预览
    const reader = new FileReader();
    reader.onload = function(e) {
      div.innerHTML = `
        <img src="${e.target.result}" alt="Photo ${index + 1}">
        <button type="button" class="remove" onclick="removePhoto(${index})">×</button>
      `;
    };
    reader.readAsDataURL(file);
    
    photoPreview.appendChild(div);
  });
  
  // 如果还有空间，显示添加按钮
  if (selectedFiles.length < 5) {
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
  
    if (selectedFiles.length > 0) {
      photoArea.classList.add('has-photos');
    } else {
      photoArea.classList.remove('has-photos');
    }
  }

  function removePhoto(index) {
    selectedFiles.splice(index, 1);
    updatePhotoPreview();
    updateSubmitButton();
  }

  function updateSubmitButton() {
    if (selectedFiles.length > 0) {
      submitBtn.disabled = false;
    } else {
      submitBtn.disabled = true;
    }
  }

  // 表单提交 - 使用 FormData 和 fetch 确保文件正确提交
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    console.log('Form submit triggered, selectedFiles:', selectedFiles.length);
    
    if (selectedFiles.length === 0) {
      alert('<?= __('inspection.photo_required', '请至少拍摄1张照片') ?>');
      return false;
    }
    
    // 创建 FormData
    const formData = new FormData();
    
    // 添加所有表单字段
    const formFields = form.querySelectorAll('input[type="hidden"]:not([name="photos[]"]), input[type="date"], input[type="radio"]:checked, textarea, select');
    formFields.forEach(field => {
      if (field.name && field.name !== 'photos[]' && field.name !== 'has_issue') {
        console.log('Adding field:', field.name, '=', field.value);
        formData.append(field.name, field.value);
      }
    });
    
    // 更新隐藏的文件输入（用于传统表单提交备用）
    const hiddenInput = document.getElementById('hidden-photo-input');
    if (hiddenInput && typeof DataTransfer !== 'undefined') {
      try {
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        hiddenInput.files = dt.files;
      } catch (e) {
        console.warn('DataTransfer not supported, will use fetch only');
      }
    }
    
    // 添加所有选中的文件到 FormData
    selectedFiles.forEach((file, index) => {
      console.log('Adding file', index + 1, ':', file.name, file.size);
      formData.append('photos[]', file);
    });
    
    console.log('Submitting form with', selectedFiles.length, 'files');
    
    // 显示加载状态
    submitBtn.disabled = true;
    const originalText = submitBtn.textContent;
    submitBtn.textContent = '<?= __('btn.submitting', '提交中...') ?>';
    
    // 使用 fetch 提交
    const submitUrl = form.action || window.location.href;
    console.log('Submitting to:', submitUrl);
    
    // 设置超时
    const timeoutId = setTimeout(() => {
      console.warn('Request timeout, trying fallback method');
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
      // 如果 fetch 超时，尝试传统表单提交
      if (hiddenInput && hiddenInput.files && hiddenInput.files.length > 0) {
        console.log('Falling back to traditional form submit');
        form.submit();
      }
    }, 30000); // 30秒超时
    
    fetch(submitUrl, {
      method: 'POST',
      body: formData,
      credentials: 'same-origin'
    })
    .then(response => {
      clearTimeout(timeoutId);
      console.log('Response received, status:', response.status, 'redirected:', response.redirected);
      
      if (response.redirected) {
        console.log('Redirecting to:', response.url);
        window.location.href = response.url;
        return;
      }
      
      if (response.ok) {
        return response.text().then(html => {
          console.log('Response HTML length:', html.length);
          // 检查是否是重定向响应
          if (html.includes('Location:') || html.includes('window.location')) {
            // 尝试提取重定向 URL
            const match = html.match(/Location:\s*([^\s]+)/i) || html.match(/window\.location\s*=\s*['"]([^'"]+)['"]/i);
            if (match) {
              window.location.href = match[1];
            } else {
              window.location.href = '/index.php?r=inspections/list&date=<?= date('Y-m-d') ?>';
            }
          } else if (html.includes('error') || html.includes('Error') || html.includes('Warning')) {
            // 显示错误页面
            document.open();
            document.write(html);
            document.close();
          } else {
            // 成功，跳转到列表页
            window.location.href = '/index.php?r=inspections/list&date=<?= date('Y-m-d') ?>';
          }
        });
      } else {
        return response.text().then(html => {
          console.error('Response error HTML:', html.substring(0, 500));
          throw new Error('提交失败: HTTP ' + response.status);
        });
      }
    })
    .catch(error => {
      clearTimeout(timeoutId);
      console.error('Fetch error:', error);
      
      // 如果 fetch 失败，尝试传统表单提交
      if (hiddenInput && hiddenInput.files && hiddenInput.files.length > 0) {
        console.log('Fetch failed, falling back to traditional form submit');
        alert('使用备用方式提交...');
        form.submit();
        return;
      }
      
      alert('<?= __('error.submit_failed', '提交失败，请重试') ?>: ' + (error.message || error));
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
    });
    
    return false;
  });

  // 按钮点击事件 - 直接触发表单提交
  submitBtn.addEventListener('click', function(e) {
    console.log('Submit button clicked, disabled:', submitBtn.disabled);
    
    if (submitBtn.disabled) {
      e.preventDefault();
      e.stopPropagation();
      return false;
    }
    
    // 检查是否有照片
    if (selectedFiles.length === 0) {
      e.preventDefault();
      e.stopPropagation();
      alert('<?= __('inspection.photo_required', '请至少拍摄1张照片') ?>');
      return false;
    }
    
    // 触发表单提交事件
    console.log('Dispatching submit event');
    const submitEvent = new Event('submit', { bubbles: true, cancelable: true });
    form.dispatchEvent(submitEvent);
  });
});
</script>

<?php include __DIR__ . '/../layout/h5_footer.php'; ?>
