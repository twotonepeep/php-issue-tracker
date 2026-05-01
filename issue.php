<?php
$pageTitle = "Issue Description"; // change per page
include 'header.php';
?>
<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/db.php';

requireLogin();

/**
 * =========================
 * VALIDATE ISSUE ID
 * =========================
 */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: issues.php");
    exit();
}

$issue_id = $_GET['id'];

/**
 * =========================
 * HANDLE FORM ACTIONS
 * =========================
 */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!verifyCSRFToken($_POST['csrf'] ?? '')) {
        die("Invalid CSRF token");
    }

    /* STATUS UPDATE (ADMIN + TECH ONLY) */
    if (isset($_POST['status']) && in_array($_SESSION["user"]["role"], ["admin", "technical"])) {

        $stmt = $pdo->prepare("UPDATE issues SET status = ? WHERE id = ?");
        $stmt->execute([$_POST['status'], $issue_id]);

        header("Location: issue.php?id=" . $issue_id);
        exit();
    }

    /* ADD COMMENT */
    if (isset($_POST['comment'])) {

        $comment = trim($_POST['comment']);

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
        }

        header("Location: issue.php?id=" . $issue_id);
        exit();
    }
}

/**
 * =========================
 * FETCH ISSUE
 * =========================
 */
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

/**
 * =========================
 * FETCH COMMENTS
 * =========================
 */
$stmt = $pdo->prepare("
    SELECT comments.*, users.name
    FROM comments
    JOIN users ON comments.user_id = users.id
    WHERE issue_id = ?
    ORDER BY comments.created_at ASC
");
$stmt->execute([$issue_id]);
$comments = $stmt->fetchAll();

$csrf = generateCSRFToken();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Issue Detail</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600&family=Nunito:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="container">

    <!-- HEADER -->
    <div class="top-bar">

        <div class="left">
            <a href="issues.php">← Back</a> |
            <a href="logout.php">Logout</a>
        </div>

        <div class="center">
            <img src="assets/logo.png" class="logo" alt="Logo">
        </div>

        <div class="right"></div>

    </div>

    <h2><?= htmlspecialchars($issue["title"]) ?></h2>

    <!-- ISSUE DETAILS -->
    <div class="panel">
        <p><strong>Status:</strong> <?= htmlspecialchars($issue["status"]) ?></p>
        <p><strong>Priority:</strong> <?= htmlspecialchars($issue["priority"]) ?></p>
        <p><strong>Category:</strong> <?= htmlspecialchars($issue["category"]) ?></p>
        <p><strong>Created by:</strong> <?= htmlspecialchars($issue["creator"]) ?></p>
    </div>

    <!-- DESCRIPTION -->
    <div class="panel">
        <h3>Description</h3>
        <p><?= htmlspecialchars($issue["description"]) ?></p>
    </div>

    <!-- COMMENTS -->
    <div class="panel">
        <h3>Comments</h3>

        <?php foreach ($comments as $c): ?>
            <p>
                <strong><?= htmlspecialchars($c["name"]) ?>:</strong>
                <?= htmlspecialchars($c["comment"]) ?>
            </p>
        <?php endforeach; ?>
    </div>

    <!-- STATUS UPDATE -->
    <?php if (in_array($_SESSION["user"]["role"], ["admin", "technical"])): ?>
        <div class="panel">
            <h3>Update Status</h3>

            <form method="POST">
                <input type="hidden" name="csrf" value="<?= $csrf ?>">

                <select name="status" class="form-control">
                    <option value="Open">Open</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Resolved">Resolved</option>
                    <option value="Closed">Closed</option>
                </select>

                <button class="btn">Update</button>
            </form>
        </div>
    <?php endif; ?>

    <!-- ADD COMMENT -->
    <div class="panel">
        <h3>Add Comment</h3>

        <form method="POST">
            <input type="hidden" name="csrf" value="<?= $csrf ?>">

            <textarea name="comment" class="form-control" placeholder="Write a comment..."></textarea>

            <button class="btn">Add Comment</button>
        </form>
    </div>

</div>

<?php include 'footer.php'; ?>

</body>
</html>