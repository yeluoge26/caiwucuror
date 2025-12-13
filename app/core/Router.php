<?php
class Router {
  public static function dispatch() {
    $r = $_GET['r'] ?? 'reports/dashboard';
    [$controller, $action] = array_pad(explode('/', $r), 2, 'index');

    $controller = ucfirst($controller) . 'Controller';
    $file = __DIR__ . '/../controllers/' . $controller . '.php';

    if (!file_exists($file)) {
      http_response_code(404);
      die('Controller not found: ' . $controller);
    }

    require_once $file;
    $obj = new $controller;

    if (!method_exists($obj, $action)) {
      http_response_code(404);
      die('Action not found: ' . $action);
    }

    call_user_func([$obj, $action]);
  }
}

