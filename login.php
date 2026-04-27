<?php
require '../app/db.php';
require '../app/auth.php';

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

<h2>Login</h2>

<form method="POST">
    <p style="color:red;"><?php echo $error; ?></p>
    <input name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button>Login</button>
</form>
