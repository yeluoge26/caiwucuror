<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/TaskAttachment.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/DB.php';

class TaskController {
  public function list() {
    Auth::requireLogin();

    // 分页参数
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = 20;
    $offset = ($page - 1) * $perPage;

    $filters = [
      'status' => $_GET['status'] ?? null,
      'type' => $_GET['type'] ?? null,
      'store' => $_GET['store'] ?? null,
      'search' => $_GET['search'] ?? null,
      'limit' => $perPage,
      'offset' => $offset
    ];

    $user = Auth::user();
    
    // 如果不是老板，只显示分配给自己的任务（包括分配给用户和分配给角色的任务）
    if ($user['role_key'] !== 'owner') {
      $filters['assigned_to_user_id'] = $user['id'];
      $filters['assigned_to_role_id'] = $user['role_id'];
    }

    $items = Task::list($filters);
    $total = Task::count($filters);
    $totalPages = ceil($total / $perPage);

    include __DIR__ . '/../views/tasks/list.php';
  }

  public function create() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager']);

    $users = User::all();
    $roles = DB::conn()->query("SELECT id, `key`, name_zh, name_vi FROM roles ORDER BY id ASC")->fetchAll();
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        $error = __('csrf.invalid');
      } else {
        $data = [
          'title' => $_POST['title'] ?? '',
          'type' => $_POST['type'] ?? 'temporary',
          'description' => $_POST['description'] ?? null,
          'due_date' => !empty($_POST['due_date']) ? $_POST['due_date'] . ' ' . ($_POST['due_time'] ?? '23:59:59') : null,
          'store' => $_POST['store'] ?? null,
          'assign_type' => $_POST['assign_type'] ?? 'user',
          'assign_role_id' => !empty($_POST['assign_role_id']) ? $_POST['assign_role_id'] : null,
          'assign_user_id' => !empty($_POST['assign_user_id']) ? $_POST['assign_user_id'] : null,
          'require_photo' => isset($_POST['require_photo']) ? 1 : 0,
          'require_attachment' => isset($_POST['require_attachment']) ? 1 : 0,
          'penalty_amount' => !empty($_POST['penalty_amount']) ? $_POST['penalty_amount'] : null,
          'reward_amount' => !empty($_POST['reward_amount']) ? $_POST['reward_amount'] : null,
          'currency' => $_POST['currency'] ?? 'VND',
          'status' => 'pending',
          'created_by' => Auth::user()['id'],
        ];

        $taskId = Task::create($data);
        if ($taskId) {
          header('Location: /index.php?r=tasks/view&id=' . $taskId);
          exit;
        } else {
          $error = __('task.create_failed');
        }
      }
    }

    include __DIR__ . '/../views/tasks/create.php';
  }

  public function view() {
    Auth::requireLogin();

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=tasks/list');
      exit;
    }

    $task = Task::find($id);
    if (!$task) {
      header('Location: /index.php?r=tasks/list');
      exit;
    }

    $user = Auth::user();
    
    // 权限检查：如果不是老板，只能查看分配给自己的任务
    if ($user['role_key'] !== 'owner' && $task['assign_user_id'] != $user['id']) {
      header('Location: /index.php?r=tasks/list');
      exit;
    }

    $attachments = TaskAttachment::listByTask($id);
    include __DIR__ . '/../views/tasks/view.php';
  }

  public function complete() {
    Auth::requireLogin();

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=tasks/list');
      exit;
    }

    $task = Task::find($id);
    if (!$task) {
      header('Location: /index.php?r=tasks/list');
      exit;
    }

    $user = Auth::user();
    
    // 只能完成分配给自己的任务
    if ($task['assign_user_id'] != $user['id']) {
      header('Location: /index.php?r=tasks/list');
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        die('CSRF invalid');
      }

      $note = $_POST['completion_note'] ?? null;
      
      // 上传附件
      $attachmentErrors = [];
      if (!empty($_FILES['attachments'])) {
        $attachmentErrors = TaskAttachment::saveUploads($id, $_FILES['attachments'], $user['id']);
      }

      Task::complete($id, $user['id'], $note);
      header('Location: /index.php?r=tasks/view&id=' . $id);
      exit;
    }

    $attachments = TaskAttachment::listByTask($id);
    include __DIR__ . '/../views/tasks/complete.php';
  }

  public function approve() {
    Auth::requireLogin();
    Auth::requireRole(['owner']);

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=tasks/list');
      exit;
    }

    $task = Task::find($id);
    if (!$task || $task['status'] !== 'completed') {
      header('Location: /index.php?r=tasks/list');
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        die('CSRF invalid');
      }

      $action = $_POST['action'] ?? 'approve';
      $note = $_POST['approval_note'] ?? null;

      if ($action === 'approve') {
        Task::approve($id, Auth::user()['id'], $note);
      } else {
        Task::reject($id, Auth::user()['id'], $note);
      }

      header('Location: /index.php?r=tasks/view&id=' . $id);
      exit;
    }

    $attachments = TaskAttachment::listByTask($id);
    include __DIR__ . '/../views/tasks/approve.php';
  }

  public function start() {
    Auth::requireLogin();

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=tasks/list');
      exit;
    }

    $task = Task::find($id);
    if (!$task) {
      header('Location: /index.php?r=tasks/list');
      exit;
    }

    $user = Auth::user();
    
    // 只能开始分配给自己的任务
    if ($task['assign_user_id'] != $user['id']) {
      header('Location: /index.php?r=tasks/list');
      exit;
    }

    Task::start($id);
    header('Location: /index.php?r=tasks/view&id=' . $id);
    exit;
  }
}

