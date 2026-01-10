<?php
/**
 * API入口文件
 * RESTful API接口，支持前后端分离
 */

// 设置错误处理
error_reporting(E_ALL);
ini_set('display_errors', 0);

// 设置时区
date_default_timezone_set('Asia/Ho_Chi_Minh');

// 处理CORS预检请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
  header('Access-Control-Max-Age: 86400');
  http_response_code(200);
  exit;
}

// 启动session（支持token认证）
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (preg_match('/Bearer\s+(.+)/i', $authHeader, $m)) {
  session_id(trim($m[1]));
}
session_start();

// 加载核心文件
require_once __DIR__ . '/../app/config/app.php';
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/core/DB.php';
require_once __DIR__ . '/../app/core/Auth.php';
require_once __DIR__ . '/../app/core/Response.php';
require_once __DIR__ . '/../app/core/ApiGuard.php';
require_once __DIR__ . '/../app/core/I18n.php';

// 初始化国际化
I18n::init();

// 全局错误处理
set_error_handler(function($errno, $errstr, $errfile, $errline) {
  Response::error("Server error: {$errstr}", 500);
});

// 全局异常处理
set_exception_handler(function($exception) {
  Response::error("Server error: " . $exception->getMessage(), 500);
});

// 路由解析
$r = $_GET['r'] ?? '';
[$ctrl, $action] = array_pad(explode('/', $r), 2, 'index');

// API控制器映射
$apiMap = [
  'auth' => 'AuthApiController',
  'transactions' => 'TransactionApiController',
  'inspections' => 'InspectionApiController',
  'employees' => 'EmployeeApiController',
  'shifts' => 'ShiftApiController',
  'tasks' => 'TaskApiController',
  'cash_closings' => 'CashClosingApiController',
  'manager' => 'ManagerApiController',
  'reports' => 'ReportApiController',
  'categories' => 'CategoryApiController',
  'payment_methods' => 'PaymentMethodApiController',
  'vendors' => 'VendorApiController',
  'assets' => 'AssetApiController',
  'materials' => 'MaterialApiController',
  'equipment_rentals' => 'EquipmentRentalApiController',
  'drinks' => 'DrinkApiController',
];

$ctrlName = $apiMap[$ctrl] ?? null;
if (!$ctrlName) {
  Response::error('API endpoint not found', 404);
}

$file = __DIR__ . '/../app/controllers/api/' . $ctrlName . '.php';
if (!file_exists($file)) {
  Response::error('API controller not found', 404);
}

require_once $file;

if (!class_exists($ctrlName)) {
  Response::error('API controller class not found', 500);
}

$obj = new $ctrlName();

if (!method_exists($obj, $action)) {
  Response::error('API action not found', 404);
}

// 执行API方法
try {
  call_user_func([$obj, $action]);
} catch (Exception $e) {
  Response::error($e->getMessage(), 500);
}
