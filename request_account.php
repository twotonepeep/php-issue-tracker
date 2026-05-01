<?php
$pageTitle = "Account Request"; // change per page
include 'header.php';
?>
<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/db.php';

/**
 * =========================
 * INITIALISE
 * =========================
 */
$message = "";
$success = false;

/**
 * =========================
 * HANDLE FORM SUBMISSION
 * =========================
 */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // CSRF protection
    if (!verifyCSRFToken($_POST['csrf'] ?? '')) {
        die("Invalid CSRF token");
    }

    // Sanitize inputs
    $employee_number = trim($_POST["employee_number"] ?? "");
    $first_name = trim($_POST["first_name"] ?? "");
    $surname = trim($_POST["surname"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $role = $_POST["role"] ?? "";

    /**
     * =========================
     * VALIDATION
     * =========================
     */

    $allowedRoles = ['user', 'technical'];

    if ($employee_number === "" || $first_name === "" || $surname === "" || $email === "" || $password === "") {
        $message = "All fields are required.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    }
    elseif (!in_array($role, $allowedRoles)) {
        $message = "Invalid role selected.";
    }
    elseif (
            strlen($password) < 8 ||
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[0-9]/', $password) ||
            !preg_match('/[\W]/', $password)
    ) {
        $message = "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";
    }
    else {

        /**
         * =========================
         * CHECK FOR EXISTING EMAIL
         * =========================
         */
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $message = "An account with this email already exists.";
        } else {

            /**
             * =========================
             * CREATE ACCOUNT (PENDING)
             * =========================
             */
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO users (employee_number, name, surname, email, password_hash, role, status)
                VALUES (?, ?, ?, ?, ?, ?, 'pending')
            ");

            $stmt->execute([
                    $employee_number,
                    $first_name,
                    $surname,
                    $email,
                    $hashedPassword,
                    $role
            ]);

            $message = "Account request submitted. Await admin approval.";
            $success = true;
        }
    }
}

/**
 * CSRF TOKEN
 */
$csrf = generateCSRFToken();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Request Account</title>

    <link rel="stylesheet" href="style.css">

    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600&family=Nunito:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .hero {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .hero h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 40px;
            color: #c084fc;
            text-shadow: 0 0 10px #9d4edd;
            margin-bottom: 10px;
        }

        .hero h2 {
            margin-bottom: 25px;
            color: #ccc;
        }

        .panel {
            width: 350px;
            margin-top: 15px;
        }

        .message {
            margin-bottom: 10px;
            color: #c084fc;
        }

        .success {
            color: #6bffb0;
        }
    </style>
</head>

<body>

<div class="hero">

    <!-- LOGO -->
    <img src="assets/logo.png" class="logo-top" alt="Logo">

    <h1>BLACK FORTRESS CYBER</h1>
    <h2>Request Account Access</h2>

    <div class="panel">

        <!-- MESSAGE -->
        <?php if ($message): ?>
            <div class="message <?= $success ? 'success' : '' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- FORM -->
        <form method="POST">

            <input type="hidden" name="csrf" value="<?= $csrf ?>">

            <input class="form-control"
                   name="employee_number"
                   placeholder="Employee Number"
                   value="<?= htmlspecialchars($_POST['employee_number'] ?? '') ?>"
                   required>

            <input class="form-control"
                   name="first_name"
                   placeholder="First Name"
                   value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>"
                   required>

            <input class="form-control"
                   name="surname"
                   placeholder="Surname"
                   value="<?= htmlspecialchars($_POST['surname'] ?? '') ?>"
                   required>

            <input class="form-control"
                   name="email"
                   type="email"
                   placeholder="Email"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                   required>

            <input class="form-control"
                   name="password"
                   type="password"
                   placeholder="Password (min 8 chars, A-Z, 0-9, symbol)"
                   required>

            <select name="role" class="form-control" required>
                <option value="">Select Role</option>
                <option value="user" <?= ($_POST['role'] ?? '') === 'user' ? 'selected' : '' ?>>User</option>
                <option value="technical" <?= ($_POST['role'] ?? '') === 'technical' ? 'selected' : '' ?>>Technical Staff</option>
            </select>

            <button type="submit" class="btn-outline">Request Account</button>

        </form>

    </div>

</div>

<?php include 'footer.php'; ?>

</body>
</html>