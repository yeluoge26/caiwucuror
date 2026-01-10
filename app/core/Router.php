<?php
class Router {
  public static function dispatch() {
    // 店长角色默认跳转到店长首页
    $defaultRoute = 'reports/dashboard';
    if (class_exists('Auth') && Auth::check()) {
      $user = Auth::user();
      if ($user && isset($user['role_key']) && $user['role_key'] === 'manager') {
        $defaultRoute = 'manager/dashboard';
      }
    }
    $r = $_GET['r'] ?? $defaultRoute;
    [$controller, $action] = array_pad(explode('/', $r), 2, 'index');

    // 路由到控制器的映射（处理单数/复数）
    $controllerMap = [
      'reports' => 'Report',
      'transactions' => 'Transaction',
      'settings' => 'Setting',
      'auth' => 'Auth',
      'tasks' => 'Task',
      'employees' => 'Employee',
      'shifts' => 'Shift',
      'cash_closings' => 'CashClosing',
      'manager' => 'Manager',
      'inspections' => 'Inspections',
      'assets' => 'Assets',
      'materials' => 'Materials',
      'drinks' => 'Drinks'
    ];

    // 如果路由在映射中，使用映射值，否则使用原值（首字母大写）
    $controllerName = $controllerMap[$controller] ?? ucfirst($controller);
    $controller = $controllerName . 'Controller';
    $file = __DIR__ . '/../controllers/' . $controller . '.php';

    if (!file_exists($file)) {
      http_response_code(404);
      die('Controller not found: ' . $controller . ' (file: ' . $file . ')');
    }

    require_once $file;
    $obj = new $controller;

    if (!method_exists($obj, $action)) {
      http_response_code(404);
      die('Action not found: ' . $action . ' in ' . $controller);
    }

    call_user_func([$obj, $action]);
  }
}

