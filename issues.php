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
 * FILTER VALIDATION
 * =========================
 */
$allowedStatus = ["Open", "In Progress", "Resolved", "Closed"];
$allowedCategory = ["Software", "New Request", "Hardware Fault", "Other"];
$allowedSort = ["newest", "oldest"];

$status = $_GET['status'] ?? "";
$category = $_GET['category'] ?? "";
$sort = $_GET['sort'] ?? "newest";

/**
 * =========================
 * BASE QUERY
 * =========================
 */
$query = "
    SELECT issues.*, users.name AS creator
    FROM issues
    JOIN users ON issues.created_by = users.id
    WHERE 1=1
";

$params = [];

/**
 * =========================
 * APPLY FILTERS (SAFE)
 * =========================
 */
if (in_array($status, $allowedStatus)) {
    $query .= " AND issues.status = ?";
    $params[] = $status;
}

if (in_array($category, $allowedCategory)) {
    $query .= " AND issues.category = ?";
    $params[] = $category;
}

/**
 * =========================
 * SORTING
 * =========================
 */
$order = ($sort === "oldest") ? "ASC" : "DESC";
$query .= " ORDER BY issues.created_at $order";

/**
 * =========================
 * EXECUTE QUERY
 * =========================
 */
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$issues = $stmt->fetchAll(PDO::FETCH_ASSOC);


/**
 * =========================
 * ADMIN PENDING COUNT
 * =========================
 */
$pendingCount = 0;
if ($_SESSION["user"]["role"] === "admin") {
    $pendingCount = $pdo->query("
        SELECT COUNT(*) FROM users WHERE status = 'pending'
    ")->fetchColumn();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Issue List</title>

    <link rel="stylesheet" href="style.css">

    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600&family=Nunito:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>

<div class="page-wrapper">

    <div class="content">

        <div class="container">

            <!-- EXISTING HEADER -->
            <div class="page-header">
                <img src="assets/logo.png" class="logo-main" alt="Logo">
                <h1 class="brand-title">BLACK FORTRESS CYBER</h1>
                <h2 class="page-title">Issue List</h2>
            </div>

            <!-- ACTION BAR -->
            <div class="top-bar">
                <div class="top-middle">
                    <a href="issue_create.php">+ Create Issue</a> |
                    <a href="logout.php">Logout</a>
                </div>

                <?php if ($_SESSION["user"]["role"] === "admin"): ?>
                    <div class="admin-box">
                        Pending Requests:
                        <strong>
                            <?= $pdo->query("SELECT COUNT(*) FROM users WHERE status='pending'")->fetchColumn(); ?>
                        </strong>
                        <br>
                        <a href="admin_requests.php">Manage</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 🔥 FILTER BAR FIX -->
            <div class="filter-bar">
                <form method="GET">
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option>Open</option>
                        <option>In Progress</option>
                        <option>Resolved</option>
                    </select>

                    <select name="category" class="form-control">
                        <option value="">All Categories</option>
                        <option>Software</option>
                        <option>Hardware Fault</option>
                        <option>New Request</option>
                        <option>Other</option>
                    </select>

                    <select name="sort" class="form-control">
                        <option value="newest">Newest</option>
                        <option value="oldest">Oldest</option>
                    </select>

                    <button class="btn-small">Filter</button>
                </form>
            </div>

            <!-- TABLE -->
            <table>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Category</th>
                        <th>Created By</th>
                    </tr>

                    <?php if (empty($issues)): ?>
                        <tr>
                            <td colspan="6" style="text-align:center;">No issues found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($issues as $issue): ?>
                            <tr>
                                <td><?= $issue["id"] ?></td>

                                <td>
                                    <a href="issue.php?id=<?= $issue["id"] ?>">
                                        <?= htmlspecialchars($issue["title"]) ?>
                                    </a>
                                </td>

                                <td><?= htmlspecialchars($issue["priority"]) ?></td>
                                <td><?= htmlspecialchars($issue["status"]) ?></td>
                                <td><?= htmlspecialchars($issue["category"]) ?></td>
                                <td><?= htmlspecialchars($issue["creator"] ?? "Unknown") ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </table>


            </table>

        </div>

    </div>

    <?php include '../public/footer.php'; ?>

</div>

</body>

</html>