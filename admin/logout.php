<?php
/**
 * Destroy the admin session and bounce back to the login page.
 */

require_once __DIR__ . '/../includes/auth.php';
logout_admin();
header('Location: login.php');
exit;
