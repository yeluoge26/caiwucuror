<?php
require_once __DIR__ . '/../core/DB.php';

class DrinkRecipe {
  public static function getByDrink($drinkId) {
    $stmt = DB::conn()->prepare("
      SELECT dr.*, m.name as material_name, m.unit
      FROM drink_recipes dr
      JOIN materials m ON dr.material_id = m.id
      WHERE dr.drink_id = ?
      ORDER BY m.name ASC
    ");
    $stmt->execute([$drinkId]);
    return $stmt->fetchAll();
  }

  public static function replace($drinkId, $items) {
    $conn = DB::conn();
    $conn->beginTransaction();
    try {
      $conn->prepare("DELETE FROM drink_recipes WHERE drink_id = ?")->execute([$drinkId]);
      $stmt = $conn->prepare("INSERT INTO drink_recipes (drink_id, material_id, amount) VALUES (?, ?, ?)");
      foreach ($items as $item) {
        $stmt->execute([$drinkId, $item['material_id'], $item['amount']]);
      }
      $conn->commit();
      return true;
    } catch (Exception $e) {
      $conn->rollBack();
      return false;
    }
  }
}
