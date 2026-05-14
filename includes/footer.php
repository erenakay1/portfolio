<?php
// Reuse $BASE from header context if available; otherwise recompute.
if (!isset($BASE)) {
    $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    if (substr($scriptDir, -6) === '/admin' || substr($scriptDir, -4) === '/api') {
        $scriptDir = substr($scriptDir, 0, strrpos($scriptDir, '/'));
    }
    $BASE = $scriptDir === '' ? '' : $scriptDir;
}
?>
</main>

<footer class="site-footer">
    <div class="footer-inner">
        <div>
            <strong>Eren Akay</strong>
            <p class="muted">AI Engineering Intern · Software Engineering Student</p>
        </div>
        <div class="footer-links">
            <a href="https://github.com/erenakay1" target="_blank" rel="noopener">GitHub</a>
            <a href="<?= $BASE ?>/index.php#contact">Contact</a>
            <a href="<?= $BASE ?>/admin/login.php">Admin</a>
        </div>
        <div class="muted">© <?= date('Y') ?> Eren Akay. Built with vanilla PHP &amp; JS.</div>
    </div>
</footer>

<script src="<?= $BASE ?>/assets/js/main.js" defer></script>
<?php if (!empty($includeShowcase)): ?>
<script src="<?= $BASE ?>/assets/js/showcase.js" defer></script>
<?php endif; ?>
</body>
</html>
