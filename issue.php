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

// Get issue ID FIRST
if (!isset($_GET['id'])) {
    header("Location: issues.php");
    exit();
}

$issue_id = $_GET['id'];

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // CSRF protection
    if (!verifyCSRFToken($_POST['csrf'] ?? '')) {
        die("Invalid CSRF token");
    }

    // Admin status update
    if (isset($_POST['status']) && $_SESSION["user"]["role"] === 'admin') {

        $stmt = $pdo->prepare("UPDATE issues SET status = ? WHERE id = ?");
        $stmt->execute([$_POST['status'], $issue_id]);

        header("Location: issue.php?id=" . $issue_id);
        exit();
    }

    // Add comment
    if (isset($_POST['comment'])) {

        $comment = trim($_POST["comment"]);

        if ($comment !== "") {
            $stmt = $pdo->prepare("
                INSERT INTO comments (issue_id, user_id, comment)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([
                    $issue_id,
                    $_SESSION["user"]["id"],
                    $comment
            ]);

            header("Location: issue.php?id=" . $issue_id);
            exit();
        }
    }
}

// Get issue
$stmt = $pdo->prepare("
    SELECT issues.*, users.name AS creator
    FROM issues
    JOIN users ON issues.created_by = users.id
    WHERE issues.id = ?
");
$stmt->execute([$issue_id]);
$issue = $stmt->fetch();

if (!$issue) {
    die("Issue not found.");
}

// Get comments
$stmt = $pdo->prepare("
    SELECT comments.*, users.name
    FROM comments
    JOIN users ON comments.user_id = users.id
    WHERE issue_id = ?
    ORDER BY comments.created_at ASC
");
$stmt->execute([$issue_id]);
$comments = $stmt->fetchAll();
?>

<div class="container">

    <h2>Issue Title</h2>

    <!-- description -->
    <!-- comments -->
    <!-- forms -->


<h2><?php echo htmlspecialchars($issue["title"]); ?></h2>

<p><strong>Status:</strong> <?php echo $issue["status"]; ?></p>
<p><strong>Priority:</strong> <?php echo $issue["priority"]; ?></p>
<p><strong>Created by:</strong> <?php echo $issue["creator"]; ?></p>

<hr>

<h3>Description</h3>
<p><?php echo nl2br(htmlspecialchars($issue["description"])); ?></p>

<hr>

<h3>Comments</h3>

<?php foreach ($comments as $c): ?>
    <p>
        <strong><?php echo $c["name"]; ?>:</strong>
        <?php echo htmlspecialchars($c["comment"]); ?>
    </p>
<?php endforeach; ?>

<hr>

<!-- Admin status update -->
<?php if ($_SESSION["user"]["role"] === 'admin'): ?>
    <h3>Update Status</h3>
    <form method="POST">
        <input type="hidden" name="csrf" value="<?php echo generateCSRFToken(); ?>">

        <select name="status">
            <option>Open</option>
            <option>In Progress</option>
            <option>Awaiting User</option>
            <option>Resolved</option>
            <option>Closed</option>
        </select>

        <button>Update Status</button>
    </form>
<?php endif; ?>

<hr>

<h3>Add Comment</h3>

<form method="POST">
    <input type="hidden" name="csrf" value="<?php echo generateCSRFToken(); ?>">

    <textarea name="comment" required></textarea><br><br>
    <button>Add Comment</button>
</form>

<br>
<a href="issues.php">Back to Issues</a>

<p><strong>Category:</strong> <?php echo $issue["category"]; ?></p>
</div>
</body>
</html>