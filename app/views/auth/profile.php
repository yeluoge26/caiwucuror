<?php
$title = __('nav.profile', '我的');
$show_back = true;
include __DIR__ . '/../layout/h5_header.php';

$user = Auth::user();
$currentLang = I18n::current();
?>

<!-- 用户信息卡片 -->
<div class="h5-card">
  <div class="h5-card-title">👤 <?= __('profile.user_info', '用户信息') ?></div>
  
  <div class="h5-form-group">
    <label><?= __('profile.username', '用户名') ?></label>
    <div style="padding: 10px; background: #f5f5f5; border-radius: 6px; color: #333;">
      <?= htmlspecialchars($user['username']) ?>
    </div>
  </div>

  <div class="h5-form-group">
    <label><?= __('profile.display_name', '显示名称') ?></label>
    <div style="padding: 10px; background: #f5f5f5; border-radius: 6px; color: #333;">
      <?= htmlspecialchars($user['display_name']) ?>
    </div>
  </div>

  <div class="h5-form-group">
    <label><?= __('profile.role', '角色') ?></label>
    <div style="padding: 10px; background: #f5f5f5; border-radius: 6px; color: #333;">
      <?php
      if ($currentLang === 'zh') {
        echo htmlspecialchars($user['role_name_zh'] ?? $user['role_key']);
      } else {
        echo htmlspecialchars($user['role_name_vi'] ?? $user['role_key']);
      }
      ?>
    </div>
  </div>

  <div class="h5-form-group">
    <label><?= __('profile.status', '状态') ?></label>
    <div style="padding: 10px; background: #f5f5f5; border-radius: 6px; color: #333;">
      <?php if ($user['is_active']): ?>
        <span style="color: #27ae60;">✅ <?= __('profile.active', '激活') ?></span>
      <?php else: ?>
        <span style="color: #e74c3c;">❌ <?= __('profile.inactive', '未激活') ?></span>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- 权限信息卡片 -->
<div class="h5-card">
  <div class="h5-card-title">🔐 <?= __('profile.permissions', '权限') ?></div>
  
  <div class="h5-form-group">
    <label><?= __('profile.role_key', '角色标识') ?></label>
    <div style="padding: 10px; background: #f5f5f5; border-radius: 6px; color: #333; font-family: monospace;">
      <?= htmlspecialchars($user['role_key']) ?>
    </div>
  </div>

  <div class="h5-form-group">
    <label><?= __('profile.permission_desc', '权限说明') ?></label>
    <div style="padding: 10px; background: #f0f7ff; border-radius: 6px; color: #333; font-size: 14px; line-height: 1.6;">
      <?php
      $permissions = [
        'owner' => __('profile.perm_owner', '老板：拥有所有权限'),
        'manager' => __('profile.perm_manager', '店长：可以管理店铺日常运营'),
        'accountant' => __('profile.perm_accountant', '财务：可以管理财务相关事务'),
        'staff' => __('profile.perm_staff', '员工：可以查看和创建自己的记录')
      ];
      echo htmlspecialchars($permissions[$user['role_key']] ?? __('profile.perm_unknown', '未知权限'));
      ?>
    </div>
  </div>
</div>

<!-- 语言切换卡片 -->
<div class="h5-card">
  <div class="h5-card-title">🌐 <?= __('profile.language', '语言设置') ?></div>
  
  <div class="h5-form-group">
    <label><?= __('profile.current_language', '当前语言') ?></label>
    <div style="padding: 10px; background: #f5f5f5; border-radius: 6px; color: #333; margin-bottom: 12px;">
      <?php
      $langNames = [
        'zh' => __('profile.lang_zh', '中文'),
        'vi' => __('profile.lang_vi', 'Tiếng Việt')
      ];
      echo htmlspecialchars($langNames[$currentLang] ?? $currentLang);
      ?>
    </div>
  </div>

  <div style="display: flex; gap: 12px;">
    <a href="/index.php?r=auth/profile&lang=vi" 
       class="h5-btn <?= $currentLang === 'vi' ? 'h5-btn-success' : '' ?>" 
       style="flex: 1; text-align: center; text-decoration: none; <?= $currentLang === 'vi' ? '' : 'background: #95a5a6;' ?>">
      🇻🇳 Tiếng Việt
    </a>
    <a href="/index.php?r=auth/profile&lang=zh" 
       class="h5-btn <?= $currentLang === 'zh' ? 'h5-btn-success' : '' ?>" 
       style="flex: 1; text-align: center; text-decoration: none; <?= $currentLang === 'zh' ? '' : 'background: #95a5a6;' ?>">
      🇨🇳 中文
    </a>
  </div>
</div>

<!-- 操作按钮 -->
<div class="h5-card">
  <a href="/index.php?r=auth/logout" class="h5-btn" style="background: #e74c3c; text-align: center; text-decoration: none;">
    🚪 <?= __('nav.logout', '退出登录') ?>
  </a>
</div>

<?php include __DIR__ . '/../layout/h5_footer.php'; ?>



