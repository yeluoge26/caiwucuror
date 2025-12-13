<?php
class DB {
  private static $conn = null;

  public static function conn() {
    if (self::$conn === null) {
      $config = require __DIR__ . '/../config/db.php';
      try {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        self::$conn = new PDO($dsn, $config['user'], $config['pass'], [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::ATTR_EMULATE_PREPARES => false,
        ]);
      } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
      }
    }
    return self::$conn;
  }
}

