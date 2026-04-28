<?php
require_once '../app/db.php';
require_once '../app/auth.php';
requireLogin();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $priority = $_POST["priority"];

    if ($title === "" || $description === "") {
        $error = "All fields are required.";
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO issues (title, description, priority, created_by)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->execute([
            $title,
            $description,
            $priority,
            $_SESSION["user"]["id"]
        ]);

        header("Location: issues.php");
        exit();
    }
}
?>

<h2>Create Issue</h2>

<p style="color:red;"><?php echo $error; ?></p>

<form method="POST">
    <input name="title" placeholder="Issue Title" required><br><br>

    <textarea name="description" placeholder="Description" required></textarea><br><br>

    <select name="priority">
        <option>Low</option>
        <option>Medium</option>
        <option>High</option>
        <option>Critical</option>
    </select><br><br>

    <button>Create Issue</button>
</form>

<br>
<a href="issues.php">Back to Issues</a>
