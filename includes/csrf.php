<?php
/**
 * CSRF token generation and verification.
 * Tokens are bound to the session and rotated on a successful state-changing action.
 */

require_once __DIR__ . '/auth.php';

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    $token = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

function csrf_verify(?string $token): bool {
    if (!is_string($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Verify a CSRF token from POST; on failure, send 403 and exit.
 * Use for HTML form submissions.
 */
function csrf_require_post(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit('Invalid CSRF token.');
    }
}
