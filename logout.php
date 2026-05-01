<?php
require_once __DIR__ . '/../app/auth.php';

/**
 * =========================
 * LOGOUT USER
 * =========================
 * Completely destroys the session securely
 */

// Clear all session variables
$_SESSION = [];

// Destroy session
session_destroy();

/**
 * =========================
 * OPTIONAL: REGENERATE SESSION
 * =========================
 * Prevents session reuse
 */
session_start();
session_regenerate_id(true);

/**
 * =========================
 * REDIRECT TO LOGIN PAGE
 * =========================
 */
header("Location: index.php");
exit();