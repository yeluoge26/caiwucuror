<?php
// Lightweight API entrypoint (REST-style JSON)
// Auth via PHP session; client sends Authorization: Bearer <token>

// Allow token-based session
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (preg_match('/Bearer\s+(.+)/i', $authHeader, $m)) {
  session_id(trim($m[1]));
}
session_start();

require_once __DIR__ . '/../app/config/app.php';
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/core/DB.php';
require_once __DIR__ . '/../app/core/Auth.php';
require_once __DIR__ . '/../app/core/Response.php';
require_once __DIR__ . '/../app/core/Csrf.php';

$r = $_GET['r'] ?? '';
[$ctrl, $action] = array_pad(explode('/', $r), 2, 'index');
$map = [
  'auth' => 'AuthApiController',
  'transactions' => 'TransactionApiController',
  'inspections' => 'InspectionApiController',
];
$ctrlName = $map[$ctrl] ?? null;
if (!$ctrlName) {
  Response::json(['error' => 'Not found'], 404);
}

$file = __DIR__ . '/../app/controllers/api/' . $ctrlName . '.php';
if (!file_exists($file)) {
  Response::json(['error' => 'Not found'], 404);
}
require_once $file;
$obj = new $ctrlName();

if (!method_exists($obj, $action)) {
  Response::json(['error' => 'Not found'], 404);
}

call_user_func([$obj, $action]);
