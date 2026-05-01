<?php
/**
 * =========================
 * PAGE TITLE HANDLING
 * =========================
 * Allows dynamic titles per page
 */
$pageTitle = $pageTitle ?? "Issue Tracker";
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <!-- =========================
         META
    ========================= -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle) ?></title>

    <!-- =========================
         STYLES
    ========================= -->
    <link rel="stylesheet" href="style.css">

    <!-- =========================
         FONTS
    ========================= -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600&family=Nunito:wght@300;400;600&display=swap" rel="stylesheet">

</head>

<body>
