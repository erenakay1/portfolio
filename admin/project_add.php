<?php
/**
 * Create a new project. Validates server-side; uses prepared statements.
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';

require_admin();

$errors = [];
$values = [
    'title'             => '',
    'short_description' => '',
    'long_description'  => '',
    'tech_stack'        => '',
    'github_url'        => '',
    'live_url'          => '',
    'image_url'         => '',
    'category'          => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Invalid CSRF token. Please retry.';
    } else {
        foreach ($values as $k => $_) {
            $values[$k] = trim((string) ($_POST[$k] ?? ''));
        }

        if ($values['title']             === '' || mb_strlen($values['title']) > 150)             $errors[] = 'Title is required (max 150).';
        if ($values['short_description'] === '' || mb_strlen($values['short_description']) > 300) $errors[] = 'Short description is required (max 300).';
        if ($values['long_description']  === '')                                                   $errors[] = 'Long description is required.';
        if ($values['tech_stack']        === '' || mb_strlen($values['tech_stack']) > 300)         $errors[] = 'Tech stack is required (max 300, comma-separated).';

        foreach (['github_url', 'live_url', 'image_url'] as $urlField) {
            if ($values[$urlField] !== '' && !filter_var($values[$urlField], FILTER_VALIDATE_URL)) {
                $errors[] = ucfirst(str_replace('_', ' ', $urlField)) . ' must be a valid URL.';
            }
        }

        if (!$errors) {
            try {
                $stmt = db()->prepare(
                    'INSERT INTO projects (title, short_description, long_description, tech_stack,
                                           github_url, live_url, image_url, category)
                     VALUES (:title, :short_description, :long_description, :tech_stack,
                             :github_url, :live_url, :image_url, :category)'
                );
                $stmt->execute([
                    ':title'             => $values['title'],
                    ':short_description' => $values['short_description'],
                    ':long_description'  => $values['long_description'],
                    ':tech_stack'        => $values['tech_stack'],
                    ':github_url'        => $values['github_url'] ?: null,
                    ':live_url'          => $values['live_url']   ?: null,
                    ':image_url'         => $values['image_url']  ?: null,
                    ':category'          => $values['category']   ?: null,
                ]);
                $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Project added.'];
                header('Location: dashboard.php');
                exit;
            } catch (Throwable $e) {
                error_log('project_add error: ' . $e->getMessage());
                $errors[] = 'Could not save project.';
            }
        }
    }
}

$pageTitle = 'Add Project';
include __DIR__ . '/../includes/header.php';
?>

<div class="editor-wrap">
    <div class="glass">
        <h1>Add Project</h1>
        <a class="muted" href="dashboard.php">← Back to dashboard</a>

        <?php foreach ($errors as $err): ?>
            <div class="flash error" style="margin-top:1rem;">
                <?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endforeach; ?>

        <form method="post" action="project_add.php" style="margin-top:1.5rem;">
            <?= csrf_field() ?>
            <?php include __DIR__ . '/_project_form_fields.php'; ?>
            <div class="form-actions">
                <button type="submit" class="btn primary">Save project</button>
                <a class="btn ghost" href="dashboard.php">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
