<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/db.php';

/**
 * =========================
 * HANDLE LOGIN REQUEST
 * =========================
 */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Sanitize inputs
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    /**
     * =========================
     * BASIC VALIDATION
     * =========================
     */
    if ($email === "" || $password === "") {
        header("Location: index.php?error=1");
        exit();
    }

    /**
     * =========================
     * FETCH USER
     * =========================
     */
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    /**
     * =========================
     * VERIFY PASSWORD
     * =========================
     */
    if ($user && password_verify($password, $user["password_hash"])) {

        /**
         * =========================
         * ACCOUNT STATUS CHECK
         * =========================
         */
        if ($user["status"] !== "active") {
            header("Location: index.php?error=notapproved");
            exit();
        }

        /**
         * =========================
         * SESSION SECURITY
         * =========================
         * Prevent session fixation
         */
        regenerateSession();

        /**
         * =========================
         * STORE USER SESSION
         * =========================
         */
        $_SESSION["user"] = [
            "id"   => $user["id"],
            "name" => $user["name"],
            "role" => $user["role"]
        ];

        /**
         * =========================
         * REDIRECT TO DASHBOARD
         * =========================
         */
        header("Location: issues.php");
        exit();
    }

    /**
     * =========================
     * INVALID LOGIN
     * =========================
     */
    header("Location: index.php?error=1");
    exit();
}