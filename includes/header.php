<?php
/**
 * Shared HTML head + top navigation.
 * Expects $pageTitle (optional) before include.
 */

require_once __DIR__ . '/auth.php';

$title = isset($pageTitle) ? $pageTitle . ' — Eren Akay' : 'Eren Akay — Portfolio';

// Build a base URL that works whether the app sits at / or /portfolio.
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
if (substr($scriptDir, -6) === '/admin' || substr($scriptDir, -4) === '/api') {
    $scriptDir = substr($scriptDir, 0, strrpos($scriptDir, '/'));
}
$BASE = $scriptDir === '' ? '' : $scriptDir;

// Determine theme from cookie so the first paint matches the user's preference.
$initialTheme = ($_COOKIE['theme'] ?? 'light') === 'dark' ? 'dark' : 'light';
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?= $initialTheme ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Eren Akay — AI Engineering Intern, Software Engineering student, and full-stack developer.">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $BASE ?>/assets/css/style.css">
    <script>
        // Apply persisted theme BEFORE first paint to avoid flash.
        (function() {
            try {
                var t = localStorage.getItem('theme');
                if (t === 'dark' || t === 'light') {
                    document.documentElement.dataset.theme = t;
                    if (t === 'dark') document.documentElement.classList.add('dark-mode');
                } else if (document.documentElement.dataset.theme === 'dark') {
                    document.documentElement.classList.add('dark-mode');
                }
            } catch (e) {}
        })();
    </script>
</head>
<body class="<?= $initialTheme === 'dark' ? 'dark-mode' : '' ?>">
<div class="animated-bg" aria-hidden="true"></div>

<nav class="topnav" id="topnav">
    <div class="topnav-inner">
        <a href="<?= $BASE ?>/index.php" class="brand">
            <span class="brand-dot"></span>
            <span class="brand-text">Eren<span class="highlight">.</span></span>
        </a>
        <ul class="nav-links">
            <li><a href="<?= $BASE ?>/index.php#home">Home</a></li>
            <li><a href="<?= $BASE ?>/index.php#about">About</a></li>
            <li><a href="<?= $BASE ?>/index.php#projects">Projects</a></li>
            <li><a href="<?= $BASE ?>/index.php#showcase">JS Demos</a></li>
            <li><a href="<?= $BASE ?>/index.php#contact">Contact</a></li>
            <?php if (is_logged_in()): ?>
                <li><a href="<?= $BASE ?>/admin/dashboard.php" class="nav-admin">Dashboard</a></li>
            <?php endif; ?>
        </ul>
        <button id="theme-toggle" class="theme-btn" type="button" aria-label="Toggle dark mode">
            <?= $initialTheme === 'dark' ? '☀️ Light' : '🌙 Dark' ?>
        </button>
        <button id="nav-burger" class="nav-burger" type="button" aria-label="Open menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>

<main class="site-main">
