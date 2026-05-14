<?php
/**
 * Public homepage: hero, about, projects (AJAX), contact form, JS showcase.
 */

require_once __DIR__ . '/includes/csrf.php';

$pageTitle = 'Home';
$csrfToken = csrf_token();
include __DIR__ . '/includes/header.php';
?>

<!-- ============== HERO ============== -->
<section id="home" class="hero">
    <div class="hero-grid">
        <div class="hero-text">
            <p class="eyebrow">Hi, I'm</p>
            <h1>Eren <span class="highlight">Akay</span>.</h1>
            <h2 class="hero-subtitle">
                AI Engineering Intern <span class="muted">@ UtaiSOFT</span> ·
                Software Engineering Student <span class="muted">@ Haliç University</span>
            </h2>
            <p class="hero-bio">
                3rd-year SE student building production AI systems. Currently working on dynamic
                tool retrieval for 1000+ tool LLM agents at UtaiSOFT. Background spans .NET
                backend engineering, RAG architectures, and LangGraph multi-agent systems.
            </p>
            <div class="hero-actions">
                <a href="#projects" class="btn primary">View Projects</a>
                <a href="#contact" class="btn ghost">Get in Touch</a>
            </div>
            <div class="hero-meta">
                <span>📍 Istanbul, Turkey</span>
                <span>🌐 Turkish · English</span>
                <a href="https://github.com/erenakay1" target="_blank" rel="noopener">⚡ github.com/erenakay1</a>
            </div>
        </div>
        <div class="hero-card glass">
            <div class="terminal-dots"><span></span><span></span><span></span></div>
            <pre class="terminal"><code>$ whoami
<span class="t-green">eren@portfolio</span>:~$ cat about.json
{
  "role"     : "AI Engineering Intern",
  "company"  : "UtaiSOFT",
  "studying" : "Software Engineering",
  "school"   : "Haliç University",
  "focus"    : ["RAG", "LangGraph", "Tool Retrieval"],
  "stack"    : ["Python", ".NET", "Postgres"],
  "ships"    : true
}
$ <span class="t-cursor">_</span></code></pre>
        </div>
    </div>
</section>

<!-- ============== ABOUT ============== -->
<section id="about" class="section">
    <div class="section-head">
        <h2 class="section-title">About</h2>
        <p class="section-sub">Engineer who ships — across AI, backend, and product surfaces.</p>
    </div>

    <div class="about-grid">
        <article class="glass about-card">
            <h3>What I'm doing</h3>
            <p>
                Building the <strong>Tool Search</strong> module at <strong>UtaiSOFT</strong> — a
                dynamic tool retrieval system for LLM agents that picks the right tool out of 1000+
                candidates. End-to-end ~93–94% Precision@1 via L4 hierarchical metadata enrichment
                and cross-encoder reranking.
            </p>
            <p>
                Board member at <strong>HUGİP</strong> (Haliç University's entrepreneurship club),
                where I also built and maintain the club's RAG-powered AI assistant.
            </p>
        </article>

        <article class="glass about-card">
            <h3>Skills</h3>
            <div class="skill-cloud">
                <span class="badge">Python</span>
                <span class="badge">.NET / C#</span>
                <span class="badge">LangChain</span>
                <span class="badge">LangGraph</span>
                <span class="badge">Pinecone</span>
                <span class="badge">Qdrant</span>
                <span class="badge">PostgreSQL</span>
                <span class="badge">MySQL</span>
                <span class="badge">Docker</span>
                <span class="badge">React</span>
                <span class="badge">Flutter</span>
                <span class="badge">Git</span>
            </div>
        </article>

        <article class="glass about-card">
            <h3>Today</h3>
            <ul class="dotted-list">
                <li><strong>AI Engineering Intern</strong> @ UtaiSOFT</li>
                <li><strong>Board member</strong> @ HUGİP</li>
                <li><strong>3rd-year SE student</strong> @ Haliç University</li>
                <li><strong>Languages:</strong> Turkish, English</li>
            </ul>
        </article>
    </div>
</section>

<!-- ============== PROJECTS (AJAX) ============== -->
<section id="projects" class="section">
    <div class="section-head">
        <h2 class="section-title">Projects</h2>
        <p class="section-sub">Pulled from MySQL via the Fetch API — admin can add/edit live.</p>
    </div>

    <div class="filter-bar">
        <label for="filter-category" class="muted">Filter by category:</label>
        <select id="filter-category" aria-label="Filter projects by category">
            <option value="All">All</option>
            <option value="AI">AI</option>
            <option value="Web">Web</option>
            <option value="Mobile">Mobile</option>
            <option value="Web/Backend">Web/Backend</option>
            <option value="Mobile/Backend">Mobile/Backend</option>
        </select>
        <span id="projects-status" class="muted" role="status" aria-live="polite">Loading projects…</span>
    </div>

    <div id="projects-grid" class="projects-grid">
        <!-- Cards injected by main.js -->
    </div>
</section>

<!-- ============== CONTACT (AJAX) ============== -->
<section id="contact" class="section">
    <div class="section-head">
        <h2 class="section-title">Contact</h2>
        <p class="section-sub">Drop a note — validated client-side, saved server-side, delivered instantly.</p>
    </div>

    <form id="contact-form" class="glass contact-form" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <!-- Honeypot field; real users never fill this. -->
        <input type="text" name="website" id="cf-website" class="honeypot" tabindex="-1" autocomplete="off">

        <div class="form-row">
            <div class="form-group">
                <label for="cf-name">Name *</label>
                <input type="text" id="cf-name" name="name" required minlength="2" maxlength="100">
                <small class="error" data-error-for="name"></small>
            </div>
            <div class="form-group">
                <label for="cf-email">Email *</label>
                <input type="email" id="cf-email" name="email" required maxlength="150">
                <small class="error" data-error-for="email"></small>
            </div>
        </div>

        <div class="form-group">
            <label for="cf-subject">Subject *</label>
            <input type="text" id="cf-subject" name="subject" required minlength="3" maxlength="200">
            <small class="error" data-error-for="subject"></small>
        </div>

        <div class="form-group">
            <label for="cf-message">Message *</label>
            <textarea id="cf-message" name="message" rows="6" required minlength="10" maxlength="5000"></textarea>
            <small class="error" data-error-for="message"></small>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn primary" id="cf-submit">Send message</button>
            <span id="cf-status" class="form-status" role="status" aria-live="polite"></span>
        </div>
    </form>
</section>

<!-- ============== JS SHOWCASE (collapsible) ============== -->
<section id="showcase" class="section">
    <details class="glass showcase">
        <summary>
            <h2 class="section-title inline">Interactive JS Demos</h2>
            <span class="muted">BOM · DOM · Events · Timers — click to expand</span>
        </summary>

        <div class="showcase-grid">
            <div class="glass demo-card">
                <h3>Digital clock</h3>
                <p class="big-number" id="digital-clock">--:--:--</p>
                <p class="muted text-sm">setInterval — re-rendered every second.</p>
            </div>

            <div class="glass demo-card">
                <h3>Event delegation</h3>
                <p class="muted text-sm">Click any list item — one listener on the parent.</p>
                <ul id="delegation-list" class="action-list">
                    <li>🚀 Ship something today</li>
                    <li>📚 Read a paper</li>
                    <li>☕ Refill coffee</li>
                    <li>🧠 Touch grass</li>
                </ul>
                <p id="delegation-result" class="result-text">Clicked: —</p>
            </div>

            <div class="glass demo-card">
                <h3>Keyboard tracker</h3>
                <p class="muted text-sm">Press any key on your keyboard:</p>
                <div class="key-display" id="last-key">—</div>
            </div>

            <div class="glass demo-card">
                <h3>Browser info (BOM)</h3>
                <ul class="dotted-list text-sm">
                    <li><strong>URL:</strong> <span id="bom-url">—</span></li>
                    <li><strong>Language:</strong> <span id="bom-lang">—</span></li>
                    <li><strong>Resolution:</strong> <span id="bom-res">—</span></li>
                </ul>
            </div>
        </div>
    </details>
</section>

<?php
$includeShowcase = true;
include __DIR__ . '/includes/footer.php';
?>
