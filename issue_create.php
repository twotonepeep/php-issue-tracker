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

$error = "";

// 🔴 POST handling goes HERE
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // CSRF check FIRST
    if (!verifyCSRFToken($_POST['csrf'] ?? '')) {
        die("Invalid CSRF token");
    }

    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $priority = $_POST["priority"];

    if ($title === "" || $description === "") {
        $error = "All fields are required.";
    } else {
        $stmt = $pdo->prepare(
                "INSERT INTO issues (title, description, priority, category, created_by)
     VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->execute([
                $title,
                $description,
                $priority,
                $_POST["category"],
                $_SESSION["user"]["id"]
        ]);

        header("Location: issues.php");
        exit();
    }
}
?>

<!-- HTML starts AFTER -->

<div class="container">

<h2>Create Issue</h2>

<p style="color:red;"><?php echo $error; ?></p>

<form method="POST">
    <input type="hidden" name="csrf" value="<?php echo generateCSRFToken(); ?>">

    <select name="category">
        <option>Software</option>
        <option>New Request</option>
        <option>Hardware Fault</option>
        <option>Other</option>
    </select><br><br>

    <input name="title" placeholder="Issue Title" required><br><br>

    <textarea name="description" required></textarea><br><br>

    <select name="priority">
        <option>Low</option>
        <option>Medium</option>
        <option>High</option>
        <option>Critical</option>
    </select><br><br>

    <button>Create Issue</button>
</form>
</div>
</body>
</html>