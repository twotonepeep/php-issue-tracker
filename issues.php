<?php
require_once '../app/db.php';
require_once '../app/auth.php';
requireLogin();

$stmt = $pdo->query("
    SELECT issues.*, users.name AS creator
    FROM issues
    JOIN users ON issues.created_by = users.id
    ORDER BY issues.created_at DESC
");

$issues = $stmt->fetchAll();
?>

<h2>Issue List</h2>

<a href="issue_create.php">+ Create Issue</a> |
<a href="logout.php">Logout</a>

<br><br>

<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Priority</th>
        <th>Status</th>
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
            <td><?php echo $issue["creator"]; ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
require '../app/db.php';
require '../app/auth.php';
requireLogin();

if (!isset($_GET['id'])) {
    die("No issue ID provided.");
}

$issue_id = $_GET['id'];

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

// Add comment
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
?>

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

<h3>Add Comment</h3>

<form method="POST">
    <textarea name="comment" required></textarea><br><br>
    <button>Add Comment</button>
</form>

<br>
<a href="issue.php">Back to Issues</a>