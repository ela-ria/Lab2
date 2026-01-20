<?php
require_once "db.php";

// Handle quick status change (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $id = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'To Do';

    $allowed = ['To Do', 'In Progress', 'Completed'];
    if ($id > 0 && in_array($status, $allowed, true)) {
        $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }

    header("Location: index.php");
    exit;
}

// Fetch tasks
$stmt = $pdo->query("SELECT * FROM tasks ORDER BY id DESC");
$tasks = $stmt->fetchAll();

function badgeClass($status) {
    if ($status === 'In Progress') return 'bg-primary';
    if ($status === 'Completed') return 'bg-success';
    return 'bg-secondary';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Task Manager</title>

  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons (for pencil/trash) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="m-0">Task Manager</h3>
    <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addTaskModal">
      <i class="bi bi-plus-lg me-1"></i> Add Task
    </button>
  </div>

  <?php if (count($tasks) === 0): ?>
    <div class="alert alert-info">No tasks yet. Click <b>Add Task</b> to create one.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead class="table-dark">
          <tr>
            <th style="width: 60px;">#</th>
            <th>Title</th>
            <th>Description</th>
            <th style="width: 150px;">Due Date</th>
            <th style="width: 180px;">Status</th>
            <th style="width: 150px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($tasks as $t): ?>
            <tr>
              <td><?= (int)$t['id'] ?></td>
              <td><?= htmlspecialchars($t['title']) ?></td>
              <td><?= nl2br(htmlspecialchars($t['description'])) ?></td>
              <td>
                <?= $t['due_date'] ? htmlspecialchars($t['due_date']) : '<span class="text-muted">—</span>' ?>
              </td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <span class="badge <?= badgeClass($t['status']) ?>">
                    <?= htmlspecialchars($t['status']) ?>
                  </span>

                  <!-- Quick Change dropdown -->
                  <form method="POST" class="d-flex gap-2 m-0">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                      <option value="To Do" <?= $t['status']==='To Do' ? 'selected' : '' ?>>To Do</option>
                      <option value="In Progress" <?= $t['status']==='In Progress' ? 'selected' : '' ?>>In Progress</option>
                      <option value="Completed" <?= $t['status']==='Completed' ? 'selected' : '' ?>>Completed</option>
                    </select>
                  </form>
                </div>
              </td>
              <td>
                <a class="btn btn-sm btn-outline-dark" href="edit.php?id=<?= (int)$t['id'] ?>" title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
                <a class="btn btn-sm btn-danger"
                   href="delete.php?id=<?= (int)$t['id'] ?>"
                   onclick="return confirm('Delete this task?')"
                   title="Delete">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="add.php">
      <div class="modal-header">
        <h5 class="modal-title">Add Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Title</label>
          <input class="form-control" name="title" required maxlength="255" placeholder="e.g., Finish lab activity">
        </div>

        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea class="form-control" name="description" required rows="4" placeholder="Describe the task..."></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Due Date</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
            <input type="date" class="form-control" name="due_date">
          </div>
          <div class="form-text">Optional. Leave blank if no deadline.</div>
        </div>

        <div class="mb-3">
          <label class="form-label">Status</label>
          <select class="form-select" name="status">
            <option value="To Do">To Do</option>
            <option value="In Progress">In Progress</option>
            <option value="Completed">Completed</option>
          </select>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-dark">Save Task</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
