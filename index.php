<?php
$pageTitle = "Index"; // change per page
include 'header.php';
?>
<?php
/**
 * =========================
 * ERROR HANDLING (LOGIN FEEDBACK)
 * =========================
 * Displays user-friendly login messages
 */
$errorMessage = "";

if (isset($_GET['error'])) {
    if ($_GET['error'] === "notapproved") {
        $errorMessage = "Account pending admin approval.";
    } else {
        $errorMessage = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Black Fortress Cyber</title>

    <link rel="stylesheet" href="style.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #000000, #2a003f);
            color: white;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hero {
            text-align: center;
        }

        h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 48px;
            color: #c084fc;
            letter-spacing: 2px;
            margin-bottom: 10px;
            text-shadow: 0 0 10px #9d4edd;
        }

        h2 {
            font-weight: 300;
            margin-bottom: 30px;
            color: #ddd;
        }

        .login-box {
            background: rgba(0,0,0,0.6);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(128,0,128,0.3);
        }

        /* FORM INPUTS */
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;

            border: 2px solid #6a3fbf;
            border-radius: 5px;

            background: rgba(0,0,0,0.4);
            color: white;

            font-family: 'Poppins', sans-serif;
            box-sizing: border-box;
        }

        input:focus {
            outline: none;
            border-color: #9d4edd;
            box-shadow: 0 0 10px rgba(157, 78, 221, 0.4);
        }

        /* BUTTONS */
        .btn-outline {
            display: inline-block;
            width: 100%;
            padding: 10px;
            margin-top: 10px;

            text-align: center;
            text-decoration: none;

            background: transparent;
            border: 2px solid #9d4edd;
            color: #c084fc;

            border-radius: 5px;
            cursor: pointer;

            font-family: 'Poppins', sans-serif;
            transition: 0.2s;
        }

        .btn-outline:hover {
            background: #7b2cbf;
            color: white;
            box-shadow: 0 0 15px rgba(157, 78, 221, 0.6);
        }

        .tagline {
            margin-top: 15px;
            font-size: 14px;
            color: #aaa;
        }

        /* ERROR BOX */
        .error-box {
            background: rgba(255,0,0,0.2);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            color: #ff6b6b;
        }
    </style>
</head>

<body>

<div class="hero">

    <!-- =========================
         LOGO + BRANDING
    ========================= -->
    <div class="logo-center">
        <img src="assets/logo.png" class="logo-img" alt="Logo">
        <h1>BLACK FORTRESS CYBER</h1>
    </div>

    <h2>Secure. Track. Resolve.</h2>

    <!-- =========================
         LOGIN BOX
    ========================= -->
    <div class="login-box">

        <!-- ERROR DISPLAY -->
        <?php if ($errorMessage): ?>
            <div class="error-box">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>

        <!-- LOGIN FORM -->
        <form method="POST" action="login.php">
            <input name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>

            <button type="submit" class="btn-outline">Login</button>
        </form>

        <!-- REQUEST ACCOUNT -->
        <a href="request_account.php" class="btn-outline">
            Request Account Access
        </a>

        <div class="tagline">
            Internal Issue Tracking System
        </div>

    </div>

</div>

<?php include 'footer.php'; ?>

</body>
</html>