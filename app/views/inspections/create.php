<?php
require_once __DIR__ . '/../../models/Inspection.php';

$title = __('nav.inspections_create', '巡店记录');
$show_back = true;
include __DIR__ . '/../layout/h5_header.php';

// 获取今日巡店次数 - 统计当前用户今日创建的巡店记录（包括pending状态），因为店长自己创建的应该立即计入
require_once __DIR__ . '/../../core/Auth.php';
$today = date('Y-m-d');
$user = Auth::user();
$todayInspections = Inspection::list([
  'date' => $today,
  'created_by' => $user['id'] // 只统计当前用户创建的巡店记录
]);
$inspectionCount = count($todayInspections);
?>

<!-- 巡店状态提示 -->
<div class="h5-card" style="background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%); border: 2px solid #3498DB;">
  <div style="text-align: center;">
    <div style="font-size: 14px; color: #6B7280; margin-bottom: 8px; font-weight: 500;">📅 <?= __('inspection.today_inspection') ?></div>
    <div style="font-size: 28px; font-weight: 700; color: #3498DB;">
      <?= __('inspection.completed') ?>: <?= $inspectionCount ?> / 24 <?= __('inspection.times') ?>
    </div>
  </div>
</div>

<?php if (!empty($error)): ?>
<div class="h5-card" style="background: #F8D7DA; border: 2px solid #F5C6CB;">
  <div style="color: #721C24; font-weight: 500;"><?= htmlspecialchars($error) ?></div>
</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">

  <!-- 基本信息 -->
  <div class="h5-card">
    <div class="h5-card-title">📋 <?= __('inspection.basic_info', '基本信息') ?></div>
    
    <div class="h5-form-group">
      <label for="store" class="h5-form-label required"><?= __('material.store', '店面') ?></label>
      <select name="store" id="store" class="h5-form-select" required>
        <option value="coffee" selected><?= __('asset.category_coffee', '咖啡店') ?></option>
        <option value="office"><?= __('asset.category_office', '办公室') ?></option>
        <option value="whiskey"><?= __('asset.category_whiskey', '威士忌') ?></option>
      </select>
    </div>

    <div class="h5-form-group">
      <label for="floor" class="h5-form-label required"><?= __('inspection.floor', '楼层') ?></label>
      <select name="floor" id="floor" class="h5-form-select" required>
        <option value="1F" selected>1F</option>
        <option value="2F">2F</option>
        <option value="3F">3F</option>
        <option value="4F">4F</option>
      </select>
    </div>

    <div class="h5-form-group">
      <label for="visit_no" class="h5-form-label required"><?= __('inspection.visit_no', '巡店次数') ?></label>
      <select name="visit_no" id="visit_no" class="h5-form-select" required>
        <option value="1" <?= ($inspectionCount + 1) == 1 ? 'selected' : '' ?>><?= __('inspection.visit_first', '首次') ?></option>
        <option value="2" <?= ($inspectionCount + 1) == 2 ? 'selected' : '' ?>><?= __('inspection.visit_second', '二次') ?></option>
      </select>
    </div>

    <div class="h5-form-group">
      <label for="room" class="h5-form-label required"><?= __('inspection.room', '房间/区域') ?></label>
      <select name="room" id="room" class="h5-form-select" required>
        <option value="store" selected><?= __('inspection.room_store', '店面') ?></option>
        <option value="restroom"><?= __('inspection.room_restroom', '卫生间') ?></option>
        <option value="stair"><?= __('inspection.room_stair', '楼梯') ?></option>
      </select>
    </div>

    <div class="h5-form-group">
      <label for="status" class="h5-form-label required"><?= __('inspection.status', '状态') ?></label>
      <select name="status" id="status" class="h5-form-select" required>
        <option value="ok" selected><?= __('inspection.ok', 'OK') ?></option>
        <option value="issue"><?= __('inspection.issue', '问题') ?></option>
      </select>
    </div>

    <div class="h5-form-group">
      <label for="spot_date" class="h5-form-label required"><?= __('field.time', '发生时间') ?></label>
      <input type="date" name="spot_date" id="spot_date" class="h5-form-input" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" required>
      <small class="h5-hint"><?= __('inspection.no_past_date_hint', '不能选择过去的日期') ?></small>
    </div>
  </div>

  <!-- 巡店拍照区 -->
  <div class="h5-card">
    <div class="h5-card-title">📷 <?= __('inspection.photo', '巡店现场照片') ?></div>
    <div class="h5-form-group">
      <label for="photos" class="h5-form-label required"><?= __('inspection.photo', '巡店现场照片') ?></label>
      <input type="file" name="photos[]" id="photos" accept="image/*" capture="environment" multiple style="display: none;">
      <button type="button" id="selectPhotosBtn" class="h5-btn h5-btn-primary" style="margin-bottom: 12px;">
        📷 <?= __('inspection.select_photos', '选择照片') ?>
      </button>
      <small class="h5-hint required"><?= __('asset.photo_hint', '支持 JPG/PNG/WEBP/GIF，单张不超过 5MB，可多选上传。至少上传1张照片。') ?></small>
      
      <!-- 已选择的照片预览 -->
      <div id="photoPreview" class="photo-preview" style="margin-top: 16px; display: none;">
        <div style="font-weight: 600; margin-bottom: 12px; color: #1F2937; font-size: 15px;">
          <?= __('inspection.selected_photos', '已选择的照片') ?> (<span id="photoCount">0</span>)
        </div>
        <div id="photoList" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 12px; margin-bottom: 12px;"></div>
        <button type="button" id="confirmUploadBtn" class="h5-btn h5-btn-success" style="display: none;">
          ✅ <?= __('inspection.confirm_upload', '确认上传') ?>
        </button>
      </div>
      
      <!-- 上传进度 -->
      <div id="uploadProgress" style="display: none; margin-top: 15px;">
        <div style="font-weight: 600; margin-bottom: 10px; color: #2c3e50;">
          <?= __('inspection.upload_progress', '上传进度') ?>
        </div>
        <div id="uploadStatus" style="background: #f5f5f5; padding: 15px; border-radius: 8px; margin-bottom: 10px;"></div>
        <div style="background: #e0e0e0; height: 8px; border-radius: 4px; overflow: hidden;">
          <div id="progressBar" style="background: #3498db; height: 100%; width: 0%; transition: width 0.3s;"></div>
        </div>
        <div id="progressText" style="text-align: center; margin-top: 8px; font-size: 12px; color: #666;">0%</div>
      </div>
    </div>
  </div>

  <!-- 巡店说明 -->
  <div class="h5-card">
    <div class="h5-card-title">✏️ <?= __('inspection.note', '巡店说明') ?></div>
    <div class="h5-form-group">
      <label for="note" class="h5-form-label"><?= __('inspection.note', '巡店说明') ?></label>
      <textarea name="note" id="note" class="h5-form-textarea" placeholder="<?= __('inspection.note_hint', '如：吧台正常 / 后厨需注意卫生 / 高峰前检查') ?>"></textarea>
    </div>
  </div>

  <!-- 提交按钮（固定底部） -->
  <div class="fixed-bottom-btn">
    <button type="submit" id="submitBtn" class="h5-btn h5-btn-success">
      ✅ <?= __('btn.save', '保存') ?>
    </button>
  </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const fileInput = document.getElementById('photos');
  const selectBtn = document.getElementById('selectPhotosBtn');
  const photoPreview = document.getElementById('photoPreview');
  const photoList = document.getElementById('photoList');
  const photoCount = document.getElementById('photoCount');
  const uploadProgress = document.getElementById('uploadProgress');
  const uploadStatus = document.getElementById('uploadStatus');
  const progressBar = document.getElementById('progressBar');
  const progressText = document.getElementById('progressText');
  const form = document.querySelector('form');
  const submitBtn = document.getElementById('submitBtn');
  const confirmUploadBtn = document.getElementById('confirmUploadBtn');
  
  let selectedFiles = [];
  let photosConfirmed = false;
  
  // 点击按钮触发文件选择
  selectBtn.addEventListener('click', function() {
    fileInput.click();
  });
  
  // 文件选择变化
  fileInput.addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    selectedFiles = files;
    updatePhotoPreview();
  });
  
  // 更新照片预览
  function updatePhotoPreview() {
    if (selectedFiles.length === 0) {
      photoPreview.style.display = 'none';
      confirmUploadBtn.style.display = 'none';
      photosConfirmed = false;
      return;
    }
    
    photoPreview.style.display = 'block';
    photoCount.textContent = selectedFiles.length;
    photoList.innerHTML = '';
    
    // 显示确认上传按钮（如果已确认则显示为已确认状态）
    confirmUploadBtn.style.display = 'block';
    if (photosConfirmed) {
      confirmUploadBtn.textContent = '✅ <?= __('inspection.photos_confirmed', '照片已确认') ?>';
      confirmUploadBtn.style.background = '#95a5a6';
      confirmUploadBtn.disabled = true;
    } else {
      confirmUploadBtn.textContent = '✅ <?= __('inspection.confirm_upload', '确认上传') ?>';
      confirmUploadBtn.style.background = '#27ae60';
      confirmUploadBtn.disabled = false;
    }
    
    selectedFiles.forEach((file, index) => {
      const item = document.createElement('div');
      item.className = 'photo-item';
      
      const img = document.createElement('img');
      img.src = URL.createObjectURL(file);
      img.alt = file.name;
      
      const removeBtn = document.createElement('button');
      removeBtn.type = 'button';
      removeBtn.className = 'remove';
      removeBtn.textContent = '×';
      removeBtn.onclick = function() {
        selectedFiles.splice(index, 1);
        photosConfirmed = false;
        updatePhotoPreview();
      };
      
      item.appendChild(img);
      item.appendChild(removeBtn);
      photoList.appendChild(item);
    });
  }
  
  // 更新文件输入
  function updateFileInput() {
    const dt = new DataTransfer();
    selectedFiles.forEach(file => dt.items.add(file));
    fileInput.files = dt.files;
  }
  
  // 格式化文件大小
  function formatFileSize(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
  }
  
  // 确认上传按钮点击
  confirmUploadBtn.addEventListener('click', function() {
    if (selectedFiles.length === 0) {
      alert('<?= __('inspection.no_photos_selected', '请先选择照片') ?>');
      return;
    }
    photosConfirmed = true;
    confirmUploadBtn.textContent = '✅ <?= __('inspection.photos_confirmed', '照片已确认') ?>';
    confirmUploadBtn.style.background = '#95a5a6';
    confirmUploadBtn.disabled = true;
  });
  
  // 表单提交
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // 如果有照片但未确认，提示用户
    if (selectedFiles.length > 0 && !photosConfirmed) {
      alert('<?= __('inspection.please_confirm_photos', '请先点击"确认上传"按钮确认照片') ?>');
      return;
    }
    
    if (selectedFiles.length === 0) {
      if (!confirm('<?= __('inspection.no_photo_confirm', '未选择照片，确定要提交吗？') ?>')) {
        return;
      }
    }
    
    // 禁用提交按钮
    submitBtn.disabled = true;
    submitBtn.textContent = '<?= __('btn.processing', '处理中...') ?>';
    
    // 显示上传进度
    uploadProgress.style.display = 'block';
    uploadStatus.innerHTML = '';
    progressBar.style.width = '0%';
    progressText.textContent = '0%';
    
    // 创建 FormData
    const formData = new FormData();
    
    // 添加表单字段（除了文件）
    const formFields = ['store', 'floor', 'visit_no', 'room', 'status', 'note', 'spot_date', '_csrf'];
    formFields.forEach(function(fieldName) {
      const field = form.querySelector('[name="' + fieldName + '"]');
      if (field) {
        if (field.type === 'checkbox' || field.type === 'radio') {
          if (field.checked) {
            formData.append(fieldName, field.value);
          }
        } else {
          formData.append(fieldName, field.value);
        }
      }
    });
    
    // 添加文件（必须使用 photos[] 作为字段名，PHP才能正确解析为数组）
    if (selectedFiles.length > 0) {
      selectedFiles.forEach((file, index) => {
        formData.append('photos[]', file);
        console.log('Added file to FormData:', file.name, file.size, 'bytes');
      });
    }
    
    // 调试：检查FormData中的内容
    console.log('FormData entries:');
    for (let pair of formData.entries()) {
      if (pair[0] === 'photos[]') {
        console.log('  File:', pair[1].name, pair[1].size, 'bytes');
      } else {
        console.log('  ' + pair[0] + ':', pair[1]);
      }
    }
    
    // 创建 XMLHttpRequest 以显示进度
    const xhr = new XMLHttpRequest();
    
    // 上传进度
    xhr.upload.addEventListener('progress', function(e) {
      if (e.lengthComputable) {
        const percentComplete = Math.round((e.loaded / e.total) * 100);
        progressBar.style.width = percentComplete + '%';
        progressText.textContent = percentComplete + '%';
        
        // 显示当前上传的文件
        const uploadedSize = formatFileSize(e.loaded);
        const totalSize = formatFileSize(e.total);
        uploadStatus.innerHTML = '<div style="color: #3498db;">📤 <?= __('inspection.uploading', '正在上传') ?>: ' + uploadedSize + ' / ' + totalSize + '</div>';
      }
    });
    
    // 上传完成
    xhr.addEventListener('load', function() {
      if (xhr.status === 200) {
        // 尝试解析 JSON 响应
        let response;
        try {
          response = JSON.parse(xhr.responseText);
        } catch (e) {
          // 如果不是 JSON，当作普通响应处理
          response = { success: true };
        }
        
        if (response.success) {
          progressBar.style.width = '100%';
          progressText.textContent = '100%';
          console.log('Upload successful! Response:', response);
          console.log('Photo count:', response.photo_count);
          console.log('Photos:', response.photos);
          uploadStatus.innerHTML = '<div style="color: #27ae60;">✅ <?= __('inspection.upload_success', '上传成功') ?>' + 
            (response.photo_count > 0 ? ' (' + response.photo_count + ' 张照片)' : ' (0 张照片)') + '</div>';
          
          // 显示成功提示，然后跳转到首页
          setTimeout(function() {
            // 使用服务器返回的跳转URL，如果没有则根据角色判断
            const homeUrl = response.redirect_url || (<?= (Auth::user()['role_key'] ?? '') === 'manager' ? "'/index.php?r=manager/dashboard'" : "'/index.php?r=reports/dashboard'" ?>);
            window.location.href = homeUrl;
          }, 1000);
        } else {
          uploadStatus.innerHTML = '<div style="color: #e74c3c;">❌ <?= __('inspection.upload_failed', '上传失败') ?>: ' + (response.message || xhr.statusText) + '</div>';
          submitBtn.disabled = false;
          submitBtn.textContent = '✅ <?= __('btn.save', '保存') ?>';
        }
      } else if (xhr.status === 302) {
        // 处理重定向（普通表单提交的情况）
        const location = xhr.getResponseHeader('Location');
        if (location) {
          window.location.href = location;
        }
      } else {
        uploadStatus.innerHTML = '<div style="color: #e74c3c;">❌ <?= __('inspection.upload_failed', '上传失败') ?>: ' + xhr.statusText + ' (HTTP ' + xhr.status + ')</div>';
        submitBtn.disabled = false;
        submitBtn.textContent = '✅ <?= __('btn.save', '保存') ?>';
      }
    });
    
    // 上传错误
    xhr.addEventListener('error', function() {
      console.error('Upload error:', xhr.status, xhr.statusText);
      console.error('Response:', xhr.responseText);
      uploadStatus.innerHTML = '<div style="color: #e74c3c;">❌ <?= __('inspection.upload_error', '上传出错，请重试') ?>: ' + xhr.statusText + '</div>';
      submitBtn.disabled = false;
      submitBtn.textContent = '✅ <?= __('btn.save', '保存') ?>';
    });
    
    // 监听响应
    xhr.addEventListener('loadend', function() {
      console.log('Request completed. Status:', xhr.status);
      console.log('Response:', xhr.responseText);
    });
    
    // 发送请求
    xhr.open('POST', form.action || window.location.href);
    
    // 设置 AJAX 请求头，让后端知道这是 AJAX 请求
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    
    // 重要：不要设置 Content-Type，让浏览器自动设置（包含 boundary）
    // xhr.setRequestHeader('Content-Type', 'multipart/form-data'); // 不要设置！
    
    xhr.send(formData);
  });
});
</script>

<?php include __DIR__ . '/../layout/h5_footer.php'; ?>
