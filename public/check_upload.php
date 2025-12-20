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

foreach ($configs as $key => $recommended) {
    $current = ini_get($key);
    $status = '✅';
    if ($key === 'file_uploads' && $current != '1' && strtolower($current) != 'on') {
        $status = '❌';
    } elseif ($key !== 'file_uploads') {
        $currentBytes = $this->parseSize($current);
        $recommendedBytes = $this->parseSize($recommended);
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

foreach ($dirs as $name => $path) {
    $exists = is_dir($path);
    $readable = $exists && is_readable($path);
    $writable = $exists && is_writable($path);
    $perms = $exists ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A';
    
    echo "<tr>";
    echo "<td>{$name} ({$path})</td>";
    echo "<td>" . ($exists ? '✅' : '❌') . "</td>";
    echo "<td>" . ($readable ? '✅' : '❌') . "</td>";
    echo "<td>" . ($writable ? '✅' : '❌') . "</td>";
    echo "<td>{$perms}</td>";
    echo "</tr>";
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
            echo "<p>错误信息：" . error_get_last()['message'] ?? '未知错误' . "</p>";
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

