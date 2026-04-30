<?php
require_once __DIR__ . '/../app/auth.php';

// Destroy session
session_unset();
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
