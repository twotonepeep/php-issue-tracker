<!DOCTYPE html>
<html>
<head>
    <title>Issue Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/db.php';
requireLogin();

$stmt = $pdo->query("
    SELECT issues.*, users.name AS creator
    FROM issues
    JOIN users ON issues.created_by = users.id
    ORDER BY issues.created_at DESC
");

$issues = $stmt->fetchAll();
?>

<div class="container">

    <h2>Issue List</h2>

    <a href="issue_create.php">+ Create Issue</a> |
    <a href="logout.php">Logout</a>

    <table>...</table>

</div>


<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Priority</th>
        <th>Status</th>
        <th>Category</th>
        <th>Created By</th>
    </tr>

    <?php foreach ($issues as $issue): ?>
        <tr>
            <td><?php echo $issue["id"]; ?></td>
            <td>
                <a href="issue.php?id=<?php echo $issue["id"]; ?>">
                    <?php echo htmlspecialchars($issue["title"]); ?>
                </a>
            </td>
            <td><?php echo $issue["priority"]; ?></td>
            <td><?php echo $issue["status"]; ?></td>
            <td><?php echo $issue["category"]; ?></td>
            <td><?php echo $issue["creator"]; ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<div class="container">
</div>

</body>
</html>