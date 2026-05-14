<?php
/**
 * Admin dashboard: list projects (with CRUD links) and contact messages.
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';

require_admin();

// Flash from previous redirect.
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Optional: mark a message as read (server-rendered toggle).
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'mark_read') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Invalid CSRF token.'];
    } else {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = db()->prepare('UPDATE contact_messages SET is_read = 1 WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Message marked as read.'];
        }
    }
    header('Location: dashboard.php');
    exit;
}

$projects = db()->query(
    'SELECT id, title, category, created_at FROM projects ORDER BY created_at DESC, id DESC'
)->fetchAll();

$messages = db()->query(
    'SELECT id, name, email, subject, message, is_read, created_at
     FROM contact_messages
     ORDER BY created_at DESC
     LIMIT 50'
)->fetchAll();

$unread = (int) db()->query('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0')
                     ->fetchColumn();

$pageTitle = 'Dashboard';
include __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-wrap">
    <div class="dashboard-header">
        <div>
            <h1>Dashboard</h1>
            <span class="muted">
                Signed in as <strong><?= htmlspecialchars($_SESSION['admin_username'], ENT_QUOTES, 'UTF-8') ?></strong>
                · <?= $unread ?> unread message<?= $unread === 1 ? '' : 's' ?>
            </span>
        </div>
        <div style="display:flex; gap:0.5rem;">
            <a class="btn primary" href="project_add.php">+ New project</a>
            <a class="btn ghost" href="../index.php">View site</a>
            <a class="btn ghost" href="logout.php">Logout</a>
        </div>
    </div>

    <?php if ($flash): ?>
        <div class="flash <?= htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars($flash['msg'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="dashboard-grid">

        <!-- Projects -->
        <section class="glass">
            <h2 style="margin-bottom:1rem;">Projects (<?= count($projects) ?>)</h2>
            <?php if (!$projects): ?>
                <p class="muted">No projects yet. Add the first one.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr><th>Title</th><th>Category</th><th>Created</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($projects as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($p['category'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="muted text-sm"><?= htmlspecialchars($p['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="row-actions">
                                    <a class="btn small ghost"   href="project_edit.php?id=<?= (int) $p['id'] ?>">Edit</a>
                                    <a class="btn small danger"
                                       href="project_delete.php?id=<?= (int) $p['id'] ?>"
                                       onclick="return confirm('Delete this project? This cannot be undone.');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <!-- Messages -->
        <section class="glass">
            <h2 style="margin-bottom:1rem;">Contact messages (<?= count($messages) ?>)</h2>
            <?php if (!$messages): ?>
                <p class="muted">No messages yet.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr><th>From</th><th>Subject</th><th>Received</th><th></th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($messages as $m): ?>
                            <tr>
                                <td>
                                    <?php if (!$m['is_read']): ?><span class="unread-dot" title="Unread"></span><?php endif; ?>
                                    <strong><?= htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                                    <br>
                                    <span class="muted text-sm"><?= htmlspecialchars($m['email'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($m['subject'], ENT_QUOTES, 'UTF-8') ?></strong>
                                    <p class="muted text-sm" style="margin-top:0.25rem; max-width:32ch;">
                                        <?= nl2br(htmlspecialchars(mb_strimwidth($m['message'], 0, 160, '…'), ENT_QUOTES, 'UTF-8')) ?>
                                    </p>
                                </td>
                                <td class="muted text-sm"><?= htmlspecialchars($m['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?php if (!$m['is_read']): ?>
                                        <form method="post" action="dashboard.php" style="margin:0;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="mark_read">
                                            <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                                            <button class="btn small ghost" type="submit">Mark read</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="muted text-sm">read</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
