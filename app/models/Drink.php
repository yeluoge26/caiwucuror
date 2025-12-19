<?php
require_once __DIR__ . '/../core/DB.php';

class Drink {
  public static function all() {
    $stmt = DB::conn()->query("SELECT * FROM drinks ORDER BY name ASC");
    return $stmt->fetchAll();
  }

  public static function create($name, $store) {
    $stmt = DB::conn()->prepare("INSERT INTO drinks (name, store) VALUES (?, ?)");
    return $stmt->execute([$name, $store]);
  }
}
