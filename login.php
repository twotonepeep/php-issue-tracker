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

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password_hash"])) {
        $_SESSION["user"] = $user;
        header("Location: issues.php");
        exit();
    } else {
        $error = "Invalid login";
    }
}
?>

<div class="container">

    <h2>Login</h2>

    <form method="POST">
        <input name="email">
        <input type="password" name="password">
        <button>Login</button>
    </form>

</div>

</body>
</html>