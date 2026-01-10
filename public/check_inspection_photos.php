<?php
/**
 * 检查巡店照片上传目录和最近上传的照片
 */

header('Content-Type: text/html; charset=utf-8');

echo "<h2>巡店照片上传诊断</h2>";

// 1. 检查目录
$uploadDir = __DIR__ . '/uploads/inspections';
$parentDir = __DIR__ . '/uploads';

echo "<h3>1. 目录检查</h3>";
echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>目录</th><th>存在</th><th>可读</th><th>可写</th><th>权限</th></tr>";

$dirs = [
    'public' => __DIR__,
    'uploads' => $parentDir,
    'inspections' => $uploadDir
];

foreach ($dirs as $name => $path) {
    $exists = is_dir($path);
    $readable = $exists && is_readable($path);
    $writable = $exists && is_writable($path);
    $perms = $exists ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A';
    
    echo "<tr>";
    echo "<td><code>{$path}</code></td>";
    echo "<td>" . ($exists ? '✅' : '❌') . "</td>";
    echo "<td>" . ($readable ? '✅' : '❌') . "</td>";
    echo "<td>" . ($writable ? '✅' : '❌') . "</td>";
    echo "<td>{$perms}</td>";
    echo "</tr>";
}

echo "</table>";

// 2. 列出目录中的文件
echo "<h3>2. 目录中的文件</h3>";
if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    $files = array_filter($files, function($f) {
        return $f !== '.' && $f !== '..';
    });
    
    if (empty($files)) {
        echo "<p style='color: orange;'>⚠️ 目录中没有文件</p>";
    } else {
        echo "<p>找到 " . count($files) . " 个文件：</p>";
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>文件名</th><th>大小</th><th>修改时间</th><th>访问</th></tr>";
        
        foreach ($files as $file) {
            $filePath = $uploadDir . '/' . $file;
            $size = filesize($filePath);
            $modified = date('Y-m-d H:i:s', filemtime($filePath));
            $url = '/uploads/inspections/' . urlencode($file);
            
            echo "<tr>";
            echo "<td><code>{$file}</code></td>";
            echo "<td>" . round($size / 1024, 2) . " KB</td>";
            echo "<td>{$modified}</td>";
            echo "<td><a href='{$url}' target='_blank'>查看</a></td>";
            echo "</tr>";
        }
        
        echo "</table>";
    }
} else {
    echo "<p style='color: red;'>❌ 目录不存在：<code>{$uploadDir}</code></p>";
}

// 3. 检查数据库中的照片记录
echo "<h3>3. 数据库中的照片记录</h3>";
try {
    require_once __DIR__ . '/../app/core/DB.php';
    $conn = DB::conn();
    
    $stmt = $conn->query("
        SELECT ip.id, ip.inspection_id, ip.file_path, ip.file_type, ip.created_at,
               i.spot_date, i.store, i.floor
        FROM inspection_photos ip
        LEFT JOIN inspections i ON ip.inspection_id = i.id
        ORDER BY ip.created_at DESC
        LIMIT 20
    ");
    
    $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($photos)) {
        echo "<p style='color: orange;'>⚠️ 数据库中没有照片记录</p>";
    } else {
        echo "<p>找到 " . count($photos) . " 条照片记录（最近20条）：</p>";
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>巡店ID</th><th>文件路径</th><th>文件类型</th><th>创建时间</th><th>文件存在</th><th>访问</th></tr>";
        
        foreach ($photos as $photo) {
            $fullPath = __DIR__ . '/' . $photo['file_path'];
            $fileExists = file_exists($fullPath);
            $url = '/' . $photo['file_path'];
            
            echo "<tr>";
            echo "<td>{$photo['id']}</td>";
            echo "<td>{$photo['inspection_id']}</td>";
            echo "<td><code>{$photo['file_path']}</code></td>";
            echo "<td>{$photo['file_type']}</td>";
            echo "<td>{$photo['created_at']}</td>";
            echo "<td>" . ($fileExists ? '✅' : '❌') . "</td>";
            echo "<td>" . ($fileExists ? "<a href='{$url}' target='_blank'>查看</a>" : '-') . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ 数据库查询错误: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// 4. PHP 上传配置
echo "<h3>4. PHP 上传配置</h3>";
echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>配置项</th><th>值</th></tr>";

$configs = [
    'file_uploads' => ini_get('file_uploads'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'max_file_uploads' => ini_get('max_file_uploads'),
    'upload_tmp_dir' => ini_get('upload_tmp_dir') ?: '系统默认',
];

foreach ($configs as $key => $value) {
    echo "<tr>";
    echo "<td>{$key}</td>";
    echo "<td><code>" . htmlspecialchars($value) . "</code></td>";
    echo "</tr>";
}

echo "</table>";

// 5. 测试上传
echo "<h3>5. 测试上传</h3>";
echo "<form method='post' enctype='multipart/form-data' style='background: #f5f5f5; padding: 15px; border-radius: 8px;'>";
echo "<input type='file' name='test_photo' accept='image/*' required>";
echo "<button type='submit' style='margin-left: 10px; padding: 8px 15px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer;'>测试上传</button>";
echo "</form>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_photo'])) {
    $testFile = $_FILES['test_photo'];
    echo "<div style='margin-top: 15px; padding: 10px; background: #e8f5e9; border-radius: 4px;'>";
    echo "<h4>上传测试结果：</h4>";
    echo "<pre>";
    echo "文件名: " . htmlspecialchars($testFile['name']) . "\n";
    echo "文件大小: " . round($testFile['size'] / 1024, 2) . " KB\n";
    echo "临时文件: " . htmlspecialchars($testFile['tmp_name']) . "\n";
    echo "错误代码: " . $testFile['error'] . "\n";
    
    if ($testFile['error'] === UPLOAD_ERR_OK) {
        if (is_writable($uploadDir)) {
            $testName = 'test_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.jpg';
            $testPath = $uploadDir . '/' . $testName;
            
            if (move_uploaded_file($testFile['tmp_name'], $testPath)) {
                echo "✅ 测试文件上传成功！\n";
                echo "保存路径: {$testPath}\n";
                echo "访问URL: /uploads/inspections/{$testName}\n";
            } else {
                echo "❌ 移动文件失败\n";
                $lastError = error_get_last();
                echo "错误信息: " . ($lastError['message'] ?? 'unknown') . "\n";
            }
        } else {
            echo "❌ 目录不可写\n";
        }
    } else {
        $errors = [
            UPLOAD_ERR_INI_SIZE => '文件大小超过 upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => '文件大小超过表单 MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => '文件只有部分被上传',
            UPLOAD_ERR_NO_FILE => '没有文件被上传',
            UPLOAD_ERR_NO_TMP_DIR => '找不到临时文件夹',
            UPLOAD_ERR_CANT_WRITE => '文件写入失败',
            UPLOAD_ERR_EXTENSION => 'PHP扩展阻止了文件上传'
        ];
        echo "❌ 上传错误: " . ($errors[$testFile['error']] ?? "未知错误 ({$testFile['error']})") . "\n";
    }
    echo "</pre>";
    echo "</div>";
}

?>






