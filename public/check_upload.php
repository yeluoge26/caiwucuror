<?php
/**
 * 图片上传诊断脚本
 * 用于检查图片上传功能的各种可能问题
 */

// 显示所有错误
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>图片上传诊断</h2>";

// 1. 检查PHP配置
echo "<h3>1. PHP配置检查</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>配置项</th><th>当前值</th><th>建议值</th></tr>";

$configs = [
    'upload_max_filesize' => '10M',
    'post_max_size' => '20M',
    'max_file_uploads' => '20',
    'memory_limit' => '128M',
    'max_execution_time' => '30',
    'file_uploads' => 'On'
];

// 辅助函数：解析大小字符串
function parseSize($size) {
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
    $size = preg_replace('/[^0-9\.]/', '', $size);
    if ($unit) {
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    } else {
        return round($size);
    }
}

foreach ($configs as $key => $recommended) {
    $current = ini_get($key);
    $status = '✅';
    if ($key === 'file_uploads' && $current != '1' && strtolower($current) != 'on') {
        $status = '❌';
    } elseif ($key !== 'file_uploads') {
        $currentBytes = parseSize($current);
        $recommendedBytes = parseSize($recommended);
        if ($currentBytes < $recommendedBytes) {
            $status = '⚠️';
        }
    }
    echo "<tr><td>{$key}</td><td>{$current}</td><td>{$recommended} {$status}</td></tr>";
}
echo "</table>";

// 2. 检查目录权限
echo "<h3>2. 目录权限检查</h3>";
$uploadDir = __DIR__ . '/uploads/inspections';
$parentDir = dirname($uploadDir);

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>目录</th><th>存在</th><th>可读</th><th>可写</th><th>权限</th></tr>";

$dirs = [
    'public' => __DIR__,
    'uploads' => __DIR__ . '/uploads',
    'inspections' => $uploadDir
];

$phpUser = function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] : get_current_user();
$hasWritableIssue = false;
$lastPerms = 'N/A';

foreach ($dirs as $name => $path) {
    $exists = is_dir($path);
    $readable = $exists && is_readable($path);
    $writable = $exists && is_writable($path);
    $perms = $exists ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A';
    
    if (!$writable && $exists) {
        $hasWritableIssue = true;
        $lastPerms = $perms;
    }
    
    // 获取目录所有者和组
    $owner = 'N/A';
    $group = 'N/A';
    if ($exists && function_exists('posix_getpwuid') && function_exists('posix_getgrgid')) {
        $stat = stat($path);
        if ($stat) {
            $ownerInfo = posix_getpwuid($stat['uid']);
            $groupInfo = posix_getgrgid($stat['gid']);
            $owner = $ownerInfo ? $ownerInfo['name'] : $stat['uid'];
            $group = $groupInfo ? $groupInfo['name'] : $stat['gid'];
        }
    }
    
    echo "<tr>";
    echo "<td>{$name} ({$path})</td>";
    echo "<td>" . ($exists ? '✅' : '❌') . "</td>";
    echo "<td>" . ($readable ? '✅' : '❌') . "</td>";
    echo "<td>" . ($writable ? '✅' : '❌') . "</td>";
    echo "<td>{$perms}<br><small>所有者: {$owner}<br>组: {$group}</small></td>";
    echo "</tr>";
}

echo "</table>";

// 显示 PHP 运行用户信息和建议
echo "<h4>PHP 运行用户信息</h4>";
echo "<p><strong>当前 PHP 运行用户：</strong> {$phpUser}</p>";

if ($hasWritableIssue) {
    echo "<div style='background:#fff3cd; border:1px solid #ffc107; padding:15px; margin:10px 0; border-radius:4px;'>";
    echo "<h4 style='margin-top:0; color:#856404;'>⚠️ 权限修复建议</h4>";
    echo "<p style='color:#856404;'><strong>问题：</strong>目录权限为 {$lastPerms}，但 PHP 用户 '{$phpUser}' 无法写入。</p>";
    echo "<p style='color:#856404;'><strong>解决方案（按优先级）：</strong></p>";
    echo "<ol style='color:#856404;'>";
    echo "<li><strong>修改目录所有者（推荐）：</strong><br>";
    echo "<code style='background:#f8f9fa; padding:5px; border-radius:3px;'>chown -R {$phpUser}:{$phpUser} public/uploads</code><br>";
    echo "然后：<code style='background:#f8f9fa; padding:5px; border-radius:3px;'>chmod -R 755 public/uploads</code></li>";
    echo "<li><strong>将 PHP 用户添加到目录所属组：</strong><br>";
    echo "<code style='background:#f8f9fa; padding:5px; border-radius:3px;'>usermod -a -G [目录组名] {$phpUser}</code><br>";
    echo "然后重启 PHP-FPM 服务</li>";
    echo "<li><strong>临时测试（不推荐用于生产环境）：</strong><br>";
    echo "<code style='background:#f8f9fa; padding:5px; border-radius:3px;'>chmod -R 777 public/uploads</code></li>";
    echo "</ol>";
    echo "</div>";
}
echo "</table>";

// 3. 检查文件上传错误
echo "<h3>3. 文件上传测试</h3>";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    $file = $_FILES['test_file'];
    echo "<p><strong>上传的文件信息：</strong></p>";
    echo "<pre>";
    print_r($file);
    echo "</pre>";
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => '文件大小超过 upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => '文件大小超过表单 MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => '文件只有部分被上传',
            UPLOAD_ERR_NO_FILE => '没有文件被上传',
            UPLOAD_ERR_NO_TMP_DIR => '找不到临时文件夹',
            UPLOAD_ERR_CANT_WRITE => '文件写入失败',
            UPLOAD_ERR_EXTENSION => 'PHP扩展阻止了文件上传'
        ];
        echo "<p style='color:red;'><strong>错误：</strong> " . ($errors[$file['error']] ?? "未知错误 ({$file['error']})") . "</p>";
    } else {
        // 尝试移动文件
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }
        $target = $uploadDir . '/test_' . time() . '_' . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $target)) {
            echo "<p style='color:green;'><strong>✅ 上传成功！</strong> 文件已保存到：{$target}</p>";
            @unlink($target); // 删除测试文件
        } else {
            echo "<p style='color:red;'><strong>❌ 移动文件失败</strong></p>";
            $lastError = error_get_last();
            echo "<p>错误信息：" . ($lastError['message'] ?? '未知错误') . "</p>";
            echo "<p style='color:orange;'><strong>💡 提示：</strong> 这通常是目录权限问题。请检查目录权限，确保 PHP 进程有写入权限。</p>";
            echo "<p>当前 PHP 运行用户：" . (function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] : get_current_user()) . "</p>";
            echo "<p>建议执行：<code>chmod -R 755 public/uploads</code> 或 <code>chmod -R 775 public/uploads</code></p>";
        }
    }
} else {
    echo "<form method='post' enctype='multipart/form-data'>";
    echo "<p>选择一个图片文件进行测试：</p>";
    echo "<input type='file' name='test_file' accept='image/*' required>";
    echo "<button type='submit'>测试上传</button>";
    echo "</form>";
}

// 4. 检查最近的错误日志
echo "<h3>4. 最近的错误日志</h3>";
$errorLog = ini_get('error_log');
if ($errorLog && file_exists($errorLog)) {
    $lines = file($errorLog);
    $recent = array_slice($lines, -10);
    echo "<pre style='background:#f5f5f5; padding:10px; max-height:200px; overflow:auto;'>";
    echo htmlspecialchars(implode('', $recent));
    echo "</pre>";
} else {
    echo "<p>错误日志文件：{$errorLog} (不存在或无法访问)</p>";
}

