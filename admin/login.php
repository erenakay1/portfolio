<?php
/**
 * Admin login. Sessions + cookies based.
 * Hardened: prepared statements, password_verify, CSRF token, session regen.
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';

// Already logged in?
if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $error = 'Please enter both username and password.';
        } else {
            try {
                $stmt = db()->prepare(
                    'SELECT id, username, password_hash FROM admin_users WHERE username = :u LIMIT 1'
                );
                $stmt->execute([':u' => $username]);
                $admin = $stmt->fetch();

                if ($admin && password_verify($password, $admin['password_hash'])) {
                    login_admin($admin);
                    header('Location: dashboard.php');
                    exit;
                }
                // Constant-ish failure path.
                $error = 'Invalid username or password.';
            } catch (Throwable $e) {
                error_log('login error: ' . $e->getMessage());
                $error = 'Could not process the login. Try again later.';
            }
        }
    }
}

$pageTitle = 'Admin Login';
include __DIR__ . '/../includes/header.php';
?>

<div class="admin-wrap glass">
    <h1>Admin Login</h1>
    <span class="muted">Sessions + cookies · CSRF protected</span>

    <?php if ($error): ?>
        <div class="flash error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="post" action="login.php" autocomplete="off">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="login-username">Username</label>
            <input type="text" id="login-username" name="username" required maxlength="50"
                   value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>" autofocus>
        </div>

        <div class="form-group">
            <label for="login-password">Password</label>
            <input type="password" id="login-password" name="password" required maxlength="255">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn primary">Sign in</button>
            <a href="../index.php" class="muted">← Back to site</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
