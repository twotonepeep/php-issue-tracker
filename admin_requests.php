<?php
$pageTitle = "Admin Requests";
include 'header.php';

require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/db.php';

requireLogin();
requireRole("admin");

/**
 * =========================
 * HANDLE FORM ACTIONS (POST)
 * =========================
 */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!verifyCSRFToken($_POST['csrf'] ?? '')) {
        die("Invalid CSRF token");
    }

    $id = $_POST['user_id'] ?? null;

    if ($id) {

        if (isset($_POST['approve'])) {
            $stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?");
            $stmt->execute([$id]);
        }

        if (isset($_POST['reject'])) {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
        }
    }

    header("Location: admin_requests.php");
    exit();
}

/**
 * =========================
 * FETCH USERS
 * =========================
 */
$stmt = $pdo->prepare("SELECT * FROM users WHERE status = 'pending'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$csrf = generateCSRFToken();
?>

<div class="page-wrapper">

    <div class="content">

        <div class="container">

            <!-- =========================
                 TOP BAR
            ========================= -->
            <div class="top-bar">

                <div class="left">
                    <a href="issues.php">← Back to Issues</a> |
                    <a href="logout.php">Logout</a>
                </div>

                <div class="center">
                    <img src="assets/logo.png" class="logo" alt="Logo">
                </div>

                <div class="right">
                    <a href="admin_users.php" class="btn-outline">Manage All Users</a>
                </div>

            </div>

            <!-- ✅ FIXED: TITLE INSIDE CONTAINER -->
            <h2>Pending Account Requests</h2>

            <!-- =========================
                 TABLE
            ========================= -->
            <div class="table-container">

                <table>
                    <tr>
                        <th>ID</th>
                        <th>Employee #</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>

                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding:20px;">
                                No pending requests
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user["id"] ?></td>
                            <td><?= htmlspecialchars($user["employee_number"]) ?></td>
                            <td><?= htmlspecialchars($user["name"] . " " . $user["surname"]) ?></td>
                            <td><?= htmlspecialchars($user["email"]) ?></td>

                            <td>
                                <!-- APPROVE -->
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="csrf" value="<?= $csrf ?>">
                                    <input type="hidden" name="user_id" value="<?= $user["id"] ?>">
                                    <button type="submit" name="approve" class="btn-small">
                                        Approve
                                    </button>
                                </form>

                                <!-- REJECT -->
                                <form method="POST" style="display:inline;"
                                      onsubmit="return confirm('Reject this user?');">
                                    <input type="hidden" name="csrf" value="<?= $csrf ?>">
                                    <input type="hidden" name="user_id" value="<?= $user["id"] ?>">
                                    <button type="submit" name="reject" class="btn-small danger">
                                        Reject
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </table>

            </div>

        </div> <!-- END container -->

    </div> <!-- END content -->

    <?php include __DIR__ . '/../public/footer.php'; ?>

</div> <!-- END page-wrapper -->