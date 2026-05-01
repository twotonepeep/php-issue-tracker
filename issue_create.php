<?php
$pageTitle = "Issue List"; // change per page
include 'header.php';
?>
<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/db.php';

requireLogin();

/**
 * =========================
 * INITIALISE
 * =========================
 */
$error = "";

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

    // Sanitise + validate inputs
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $priority = $_POST["priority"] ?? "";
    $category = $_POST["category"] ?? "";

    // Allowed values (prevents tampering)
    $allowedPriorities = ["Low", "Medium", "High", "Critical"];
    $allowedCategories = ["Software", "New Request", "Hardware Fault", "Other"];

    if ($title === "" || $description === "") {
        $error = "All fields are required.";
    } elseif (!in_array($priority, $allowedPriorities)) {
        $error = "Invalid priority selected.";
    } elseif (!in_array($category, $allowedCategories)) {
        $error = "Invalid category selected.";
    } else {

        $stmt = $pdo->prepare("
            INSERT INTO issues (title, description, priority, category, created_by)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
                $title,
                $description,
                $priority,
                $category,
                $_SESSION["user"]["id"]
        ]);

        header("Location: issues.php");
        exit();
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
    <title>Create Issue</title>

    <link rel="stylesheet" href="style.css">

    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600&family=Nunito:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>

<div class="hero">

    <!-- =========================
         LOGO + BRANDING
    ========================= -->
    <img src="assets/logo.png" class="logo-top" alt="Logo">

    <h1>BLACK FORTRESS CYBER</h1>
    <h2>Create Issue</h2>

    <!-- =========================
         FORM PANEL
    ========================= -->
    <div class="panel">

        <!-- ERROR DISPLAY -->
        <?php if ($error): ?>
            <div class="error-box">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <!-- CSRF -->
            <input type="hidden" name="csrf" value="<?= $csrf ?>">

            <!-- CATEGORY -->
            <select name="category" class="form-control">
                <option value="Software">Software</option>
                <option value="New Request">New Request</option>
                <option value="Hardware Fault">Hardware Fault</option>
                <option value="Other">Other</option>
            </select>

            <!-- TITLE -->
            <input class="form-control"
                   name="title"
                   placeholder="Issue Title"
                   value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                   required>

            <!-- DESCRIPTION -->
            <textarea class="form-control"
                      name="description"
                      placeholder="Description"
                      required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>

            <!-- PRIORITY -->
            <select name="priority" class="form-control">
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
                <option value="Critical">Critical</option>
            </select>

            <button class="btn">Create Issue</button>

        </form>

        <!-- NAV -->
        <a href="issues.php" class="btn-outline">← Back</a>

    </div>

</div>

<?php include 'footer.php'; ?>

</body>
</html>