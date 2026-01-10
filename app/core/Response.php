<?php
class Response {
  /**
   * 发送JSON响应
   */
  public static function json($data, $statusCode = 200) {
    // 设置CORS头（允许跨域）
    self::setCorsHeaders();
    
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    
    // 统一响应格式
    $response = [
      'success' => $statusCode >= 200 && $statusCode < 300,
      'data' => $data,
      'timestamp' => date('Y-m-d H:i:s')
    ];
    
    // 如果是错误响应，调整格式
    if (!$response['success']) {
      $response['error'] = $data;
      unset($response['data']);
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
  }

  /**
   * 发送成功响应
   */
  public static function success($data = null, $message = null, $statusCode = 200) {
    self::setCorsHeaders();
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    
    $response = [
      'success' => true,
      'data' => $data,
      'message' => $message,
      'timestamp' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
  }

  /**
   * 发送错误响应
   */
  public static function error($message, $statusCode = 400, $errors = null) {
    self::setCorsHeaders();
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    
    $response = [
      'success' => false,
      'error' => $message,
      'errors' => $errors,
      'timestamp' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
  }

  /**
   * 设置CORS头
   */
  private static function setCorsHeaders() {
    $allowedOrigins = [
      'http://localhost:3000',
      'http://localhost:5173',
      'http://localhost:8080',
      'http://127.0.0.1:3000',
      'http://127.0.0.1:5173',
      'http://127.0.0.1:8080'
    ];
    
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if (in_array($origin, $allowedOrigins)) {
      header("Access-Control-Allow-Origin: {$origin}");
    } else {
      header('Access-Control-Allow-Origin: *');
    }
    
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
    
    // 处理预检请求
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
      http_response_code(200);
      exit;
    }
  }

  public static function redirect($url) {
    header('Location: ' . $url);
    exit;
  }
}

