<?php
require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$due_date = $_POST['due_date'] ?? null;
$status = $_POST['status'] ?? 'To Do';

$allowed = ['To Do', 'In Progress', 'Completed'];
if (!in_array($status, $allowed, true)) $status = 'To Do';

// Normalize empty due_date -> NULL
if ($due_date === '') $due_date = null;

if ($title === '' || $description === '') {
    die("Title and Description are required.");
}

$stmt = $pdo->prepare("INSERT INTO tasks (title, description, due_date, status) VALUES (?, ?, ?, ?)");
$stmt->execute([$title, $description, $due_date, $status]);

header("Location: index.php");
exit;
