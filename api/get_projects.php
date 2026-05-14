<?php
/**
 * Returns all projects as JSON for the public homepage's AJAX fetch.
 */

require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

try {
    $category = isset($_GET['category']) ? trim((string) $_GET['category']) : '';

    if ($category !== '' && strtolower($category) !== 'all') {
        $stmt = db()->prepare(
            'SELECT id, title, short_description, long_description, tech_stack,
                    github_url, live_url, image_url, category, created_at
             FROM projects
             WHERE category = :category
             ORDER BY created_at DESC, id DESC'
        );
        $stmt->execute([':category' => $category]);
    } else {
        $stmt = db()->query(
            'SELECT id, title, short_description, long_description, tech_stack,
                    github_url, live_url, image_url, category, created_at
             FROM projects
             ORDER BY created_at DESC, id DESC'
        );
    }

    $rows = $stmt->fetchAll();

    // Split tech_stack into an array client-side convenience.
    foreach ($rows as &$row) {
        $row['tech_stack_list'] = array_values(array_filter(array_map(
            'trim',
            explode(',', $row['tech_stack'])
        )));
    }
    unset($row);

    echo json_encode([
        'ok'       => true,
        'count'    => count($rows),
        'projects' => $rows,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(500);
    error_log('get_projects error: ' . $e->getMessage());
    echo json_encode(['ok' => false, 'error' => 'Failed to load projects.']);
}
