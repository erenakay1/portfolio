<?php
/**
 * Session bootstrap + admin authentication helpers.
 */

if (session_status() === PHP_SESSION_NONE) {
    // Harden session cookies. `secure` is only forced when HTTPS is detected,
    // otherwise local HTTP testing breaks.
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name('PORTFOLIO_SESSION');
    session_start();
}

function is_logged_in(): bool {
    return !empty($_SESSION['admin_id']) && !empty($_SESSION['admin_username']);
}

function require_admin(): void {
    if (!is_logged_in()) {
        // Resolve a base path that works whether the app sits at /portfolio or /.
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        // Strip a trailing /admin so we land at the project root + /admin/login.php.
        if (substr($base, -6) === '/admin') {
            $base = substr($base, 0, -6);
        }
        header('Location: ' . $base . '/admin/login.php');
        exit;
    }
}

function login_admin(array $adminRow): void {
    // Defeat session fixation.
    session_regenerate_id(true);
    $_SESSION['admin_id']       = (int) $adminRow['id'];
    $_SESSION['admin_username'] = $adminRow['username'];
    $_SESSION['login_time']     = time();
}

function logout_admin(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    session_destroy();
}

/**
 * Preference cookie helper for the public site (e.g. theme).
 * SameSite=Lax for InfinityFree compatibility.
 */
function set_pref_cookie(string $name, string $value, int $days = 365): void {
    setcookie($name, $value, [
        'expires'  => time() + ($days * 86400),
        'path'     => '/',
        'secure'   => false,
        'httponly' => false,
        'samesite' => 'Lax',
    ]);
}
