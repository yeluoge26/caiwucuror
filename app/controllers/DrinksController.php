<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../models/Drink.php';
require_once __DIR__ . '/../models/DrinkRecipe.php';
require_once __DIR__ . '/../models/Material.php';
require_once __DIR__ . '/../models/ConsumptionLog.php';

class DrinksController {
  public function recipes() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    $drinks = Drink::all();
    $materials = Material::all();
    $selectedId = $_GET['drink_id'] ?? ($drinks[0]['id'] ?? null);
    $recipe = $selectedId ? DrinkRecipe::getByDrink($selectedId) : [];
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        $error = __('csrf.invalid');
      } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'create_drink') {
          Drink::create($_POST['name'], $_POST['store']);
        } elseif ($action === 'save_recipe') {
          $drinkId = $_POST['drink_id'];
          $items = [];
          $matIds = $_POST['material_id'] ?? [];
          $amounts = $_POST['amount'] ?? [];
          foreach ($matIds as $idx => $mid) {
            $amt = $amounts[$idx] ?? 0;
            if ($mid && $amt > 0) {
              $items[] = ['material_id' => $mid, 'amount' => $amt];
            }
          }
          DrinkRecipe::replace($drinkId, $items);
        }
        header('Location: /index.php?r=drinks/recipes');
        exit;
      }
    }

    $drinks = Drink::all(); // refresh
    $selectedId = $_GET['drink_id'] ?? ($drinks[0]['id'] ?? null);
    $recipe = $selectedId ? DrinkRecipe::getByDrink($selectedId) : [];

    include __DIR__ . '/../views/drinks/recipes.php';
  }

  public function consume() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    $drinks = Drink::all();
    $selectedId = $_POST['drink_id'] ?? ($drinks[0]['id'] ?? null);
    $recipe = $selectedId ? DrinkRecipe::getByDrink($selectedId) : [];
    $error = null;
    $success = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        $error = __('csrf.invalid');
      } else {
        $qty = (int)($_POST['quantity'] ?? 0);
        if ($qty > 0 && $selectedId) {
          // 扣减库存
          foreach ($recipe as $line) {
            $use = $qty * $line['amount'];
            Material::deduct($line['material_id'], $use);
          }
          ConsumptionLog::create([
            'drink_id' => $selectedId,
            'quantity' => $qty,
            'occurred_at' => $_POST['occurred_at'] ?? date('Y-m-d'),
            'note' => $_POST['note'] ?? null,
            'created_by' => Auth::user()['id'],
          ]);
          $success = __('material.consume_success');
        } else {
          $error = __('material.consume_failed');
        }
      }
      // refresh recipe for selected drink
      $recipe = $selectedId ? DrinkRecipe::getByDrink($selectedId) : [];
    }

    include __DIR__ . '/../views/drinks/consume.php';
  }
}
