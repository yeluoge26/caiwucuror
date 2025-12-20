<?php
session_start();

// 设置错误日志到项目目录（在 open_basedir 限制内）
$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}
$logFile = $logDir . '/error.log';
if (file_exists($logFile) && !is_writable($logFile)) {
    @chmod($logFile, 0664);
} elseif (!file_exists($logFile)) {
    @touch($logFile);
    @chmod($logFile, 0664);
}
if (is_writable($logFile)) {
    ini_set('error_log', $logFile);
    error_log("=== Application started at " . date('Y-m-d H:i:s') . " ===");
}

require_once __DIR__ . '/../app/config/app.php';
require_once __DIR__ . '/../app/config/db.php';

require_once __DIR__ . '/../app/core/DB.php';
require_once __DIR__ . '/../app/core/Router.php';
require_once __DIR__ . '/../app/core/Auth.php';
require_once __DIR__ . '/../app/core/I18n.php';
require_once __DIR__ . '/../app/core/Csrf.php';

I18n::init();     // 初始化语言
Router::dispatch();
