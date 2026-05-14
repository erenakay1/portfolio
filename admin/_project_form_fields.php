<?php
/**
 * Shared form fields for project_add.php and project_edit.php.
 * Expects $values array in scope.
 */
?>
<div class="form-row">
    <div class="form-group">
        <label for="f-title">Title *</label>
        <input type="text" id="f-title" name="title" required maxlength="150"
               value="<?= htmlspecialchars($values['title'], ENT_QUOTES, 'UTF-8') ?>">
    </div>
    <div class="form-group">
        <label for="f-category">Category</label>
        <input type="text" id="f-category" name="category" maxlength="50"
               placeholder="AI / Web / Mobile / …"
               value="<?= htmlspecialchars($values['category'], ENT_QUOTES, 'UTF-8') ?>">
    </div>
</div>

<div class="form-group">
    <label for="f-short">Short description * <span class="muted text-sm">(max 300 chars)</span></label>
    <input type="text" id="f-short" name="short_description" required maxlength="300"
           value="<?= htmlspecialchars($values['short_description'], ENT_QUOTES, 'UTF-8') ?>">
</div>

<div class="form-group">
    <label for="f-long">Long description *</label>
    <textarea id="f-long" name="long_description" rows="6" required><?= htmlspecialchars($values['long_description'], ENT_QUOTES, 'UTF-8') ?></textarea>
</div>

<div class="form-group">
    <label for="f-tech">Tech stack * <span class="muted text-sm">(comma-separated)</span></label>
    <input type="text" id="f-tech" name="tech_stack" required maxlength="300"
           placeholder="Python, LangGraph, Pinecone"
           value="<?= htmlspecialchars($values['tech_stack'], ENT_QUOTES, 'UTF-8') ?>">
</div>

<div class="form-row">
    <div class="form-group">
        <label for="f-github">GitHub URL</label>
        <input type="url" id="f-github" name="github_url" maxlength="300"
               value="<?= htmlspecialchars($values['github_url'], ENT_QUOTES, 'UTF-8') ?>">
    </div>
    <div class="form-group">
        <label for="f-live">Live URL</label>
        <input type="url" id="f-live" name="live_url" maxlength="300"
               value="<?= htmlspecialchars($values['live_url'], ENT_QUOTES, 'UTF-8') ?>">
    </div>
</div>

<div class="form-group">
    <label for="f-image">Image URL</label>
    <input type="url" id="f-image" name="image_url" maxlength="300"
           placeholder="https://…"
           value="<?= htmlspecialchars($values['image_url'], ENT_QUOTES, 'UTF-8') ?>">
</div>
