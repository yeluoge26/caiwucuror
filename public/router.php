<?php
class Router {
  public static function dispatch() {
    $r = $_GET['r'] ?? 'transactions/list';
    [$controller, $action] = array_pad(explode('/', $r), 2, 'index');

    $controller = ucfirst($controller) . 'Controller';
    $file = __DIR__ . '/../controllers/' . $controller . '.php';

    if (!file_exists($file)) {
      http_response_code(404); exit('Controller not found');
    }

    require_once $file;
    $obj = new $controller;

    if (!method_exists($obj, $action)) {
      http_response_code(404); exit('Action not found');
    }

    call_user_func([$obj, $action]);
  }
}
