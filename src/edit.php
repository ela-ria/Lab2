<?php
require_once "db.php";

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

// If submitted, update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $due_date = $_POST['due_date'] ?? null;
    $status = $_POST['status'] ?? 'To Do';

    $allowed = ['To Do', 'In Progress', 'Completed'];
    if (!in_array($status, $allowed, true)) $status = 'To Do';
    if ($due_date === '') $due_date = null;

    if ($title === '' || $description === '') {
        $error = "Title and Description are required.";
    } else {
        $stmt = $pdo->prepare("UPDATE tasks SET title=?, description=?, due_date=?, status=? WHERE id=?");
        $stmt->execute([$title, $description, $due_date, $status, $id]);
        header("Location: index.php");
        exit;
    }
}

// Fetch existing
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->execute([$id]);
$task = $stmt->fetch();

if (!$task) {
    die("Task not found.");
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Task</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
<style>
  body {
    background-color: #fce7f3; 
  }
  </style>

<div class="container py-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="m-0">Edit your task no. <?= (int)$task['id'] ?></h3>
    <a href="index.php" class="btn btn-outline-dark">
      <i class="bi bi-arrow-left me-1"></i> Back
    </a>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Title</label>
          <input class="form-control" name="title" required maxlength="255"
                 value="<?= htmlspecialchars($task['title']) ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea class="form-control" name="description" required rows="4"><?= htmlspecialchars($task['description']) ?></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Due Date</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
            <input type="date" class="form-control" name="due_date"
                   value="<?= htmlspecialchars($task['due_date'] ?? '') ?>">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Status</label>
          <select class="form-select" name="status">
            <option value="To Do" <?= $task['status']==='To Do' ? 'selected' : '' ?>>To Do</option>
            <option value="In Progress" <?= $task['status']==='In Progress' ? 'selected' : '' ?>>In Progress</option>
            <option value="Completed" <?= $task['status']==='Completed' ? 'selected' : '' ?>>Completed</option>
          </select>
        </div>

        <button class="btn btn-dark" type="submit">Save Changes</button>
        <a class="btn btn-outline-secondary" href="index.php">Cancel</a>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
