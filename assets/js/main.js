/* =============================================================
 * Portfolio frontend — vanilla JS
 * - Dark mode toggle persisted in localStorage + cookie
 * - Mobile nav toggle
 * - AJAX project loading with category filter
 * - Contact form: client-side validation + Fetch API POST
 * - Project detail modal
 * ============================================================= */

(function () {
    'use strict';

    // ---------------- Base URL helper ----------------
    // index.php and admin pages live at different depths;
    // derive the project root from the location pathname.
    function getBase() {
        const p = window.location.pathname;
        // Find segment up to last '/' before file name or last '/'.
        const lastSlash = p.lastIndexOf('/');
        let base = lastSlash >= 0 ? p.substring(0, lastSlash) : '';
        if (base.endsWith('/admin') || base.endsWith('/api')) {
            base = base.substring(0, base.lastIndexOf('/'));
        }
        return base;
    }
    const BASE = getBase();

    // ---------------- Theme toggle ----------------
    function initTheme() {
        const btn = document.getElementById('theme-toggle');
        if (!btn) return;

        function applyTheme(theme) {
            document.documentElement.dataset.theme = theme;
            document.body.classList.toggle('dark-mode', theme === 'dark');
            btn.textContent = theme === 'dark' ? '☀️ Light' : '🌙 Dark';
            try { localStorage.setItem('theme', theme); } catch (e) {}
            // Cookie copy for server-rendered first paint.
            document.cookie = 'theme=' + theme + ';path=/;max-age=' + (365 * 86400) + ';SameSite=Lax';
        }

        const stored = (function () {
            try { return localStorage.getItem('theme'); } catch (e) { return null; }
        })();
        if (stored === 'dark' || stored === 'light') {
            applyTheme(stored);
        } else if (document.body.classList.contains('dark-mode')) {
            btn.textContent = '☀️ Light';
        }

        btn.addEventListener('click', function () {
            const current = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
            applyTheme(current === 'dark' ? 'light' : 'dark');
        });
    }

    // ---------------- Mobile nav ----------------
    function initNav() {
        const burger = document.getElementById('nav-burger');
        const links  = document.querySelector('.nav-links');
        if (!burger || !links) return;
        burger.addEventListener('click', function () {
            const open = links.classList.toggle('open');
            burger.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        // Close mobile nav after clicking a link.
        links.addEventListener('click', function (e) {
            if (e.target.tagName === 'A') {
                links.classList.remove('open');
                burger.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // ---------------- Projects (AJAX) ----------------
    const projectsGrid = document.getElementById('projects-grid');
    const projectsStatus = document.getElementById('projects-status');
    const filterSelect = document.getElementById('filter-category');

    let cachedProjects = [];

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderProjects(list) {
        if (!projectsGrid) return;
        if (!list.length) {
            projectsGrid.innerHTML =
                '<div class="empty-state">No projects in this category yet.</div>';
            return;
        }

        const cards = list.map(function (p) {
            const tech = (p.tech_stack_list || []).map(function (t) {
                return '<span class="tech-chip">' + escapeHtml(t) + '</span>';
            }).join('');

            const imageAttr = p.image_url
                ? ' data-bg="' + escapeHtml(p.image_url) + '"'
                : '';

            const gh   = p.github_url ? '<a class="btn small ghost" href="' + escapeHtml(p.github_url) + '" target="_blank" rel="noopener">GitHub</a>' : '';
            const live = p.live_url   ? '<a class="btn small primary" href="' + escapeHtml(p.live_url) + '" target="_blank" rel="noopener">Live</a>' : '';
            const cat  = p.category   ? '<span class="project-cat">' + escapeHtml(p.category) + '</span>' : '';

            return ''
                + '<article class="project-card glass" data-id="' + p.id + '">'
                +   '<div class="project-image"' + imageAttr + '>' + cat + '</div>'
                +   '<div class="project-body">'
                +     '<h3>' + escapeHtml(p.title) + '</h3>'
                +     '<p>' + escapeHtml(p.short_description) + '</p>'
                +     '<div class="project-tech">' + tech + '</div>'
                +     '<div class="project-actions">'
                +       '<button class="btn small ghost btn-details" type="button" data-id="' + p.id + '">Details</button>'
                +       gh + live
                +     '</div>'
                +   '</div>'
                + '</article>';
        }).join('');

        projectsGrid.innerHTML = cards;

        // Apply image background-image via JS so the URL doesn't have to be
        // escaped into an inline style attribute (which gets HTML-parsed and
        // would break on quotes / & characters).
        projectsGrid.querySelectorAll('.project-image[data-bg]').forEach(function (el) {
            el.style.backgroundImage = 'url("' + el.dataset.bg.replace(/"/g, '%22') + '")';
        });
    }

    function openProjectModal(project) {
        let modal = document.getElementById('project-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'project-modal';
            modal.className = 'modal-backdrop';
            modal.innerHTML =
                '<div class="modal glass" role="dialog" aria-modal="true" style="position:relative">'
                + '<button type="button" class="modal-close" aria-label="Close">×</button>'
                + '<div class="modal-body"></div>'
                + '</div>';
            document.body.appendChild(modal);
            modal.addEventListener('click', function (e) {
                if (e.target === modal || e.target.classList.contains('modal-close')) {
                    modal.classList.remove('active');
                }
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') modal.classList.remove('active');
            });
        }

        const tech = (project.tech_stack_list || []).map(function (t) {
            return '<span class="tech-chip">' + escapeHtml(t) + '</span>';
        }).join('');
        const gh   = project.github_url ? '<a class="btn small ghost" href="' + escapeHtml(project.github_url) + '" target="_blank" rel="noopener">GitHub</a>' : '';
        const live = project.live_url   ? '<a class="btn small primary" href="' + escapeHtml(project.live_url) + '" target="_blank" rel="noopener">Live</a>' : '';

        modal.querySelector('.modal-body').innerHTML =
            '<h3>' + escapeHtml(project.title) + '</h3>'
            + (project.category ? '<p class="muted text-sm" style="margin-bottom:1rem">Category: ' + escapeHtml(project.category) + '</p>' : '')
            + '<p>' + escapeHtml(project.long_description) + '</p>'
            + '<div class="project-tech" style="margin-bottom:1rem">' + tech + '</div>'
            + '<div class="project-actions">' + gh + live + '</div>';

        modal.classList.add('active');
    }

    function fetchProjects(category) {
        if (!projectsGrid) return;
        if (projectsStatus) projectsStatus.textContent = 'Loading projects…';

        const url = BASE + '/api/get_projects.php'
            + (category && category !== 'All' ? '?category=' + encodeURIComponent(category) : '');

        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function (r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(function (data) {
                if (!data.ok) throw new Error(data.error || 'Unknown error');
                cachedProjects = data.projects || [];
                renderProjects(cachedProjects);
                if (projectsStatus) {
                    projectsStatus.textContent = cachedProjects.length
                        + (cachedProjects.length === 1 ? ' project loaded.' : ' projects loaded.');
                }
            })
            .catch(function (err) {
                console.error('Project fetch failed:', err);
                if (projectsStatus) projectsStatus.textContent = 'Could not load projects.';
                projectsGrid.innerHTML =
                    '<div class="empty-state">Failed to load projects. Is the database connected?</div>';
            });
    }

    function initProjects() {
        if (!projectsGrid) return;
        fetchProjects('All');

        if (filterSelect) {
            filterSelect.addEventListener('change', function () {
                fetchProjects(filterSelect.value);
            });
        }

        projectsGrid.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-details');
            if (!btn) return;
            const id = parseInt(btn.getAttribute('data-id'), 10);
            const project = cachedProjects.find(function (p) { return parseInt(p.id, 10) === id; });
            if (project) openProjectModal(project);
        });
    }

    // ---------------- Contact form (AJAX) ----------------
    function initContactForm() {
        const form = document.getElementById('contact-form');
        if (!form) return;

        const status = document.getElementById('cf-status');
        const submit = document.getElementById('cf-submit');

        function showError(name, msg) {
            const el = form.querySelector('[data-error-for="' + name + '"]');
            const input = form.querySelector('[name="' + name + '"]');
            if (el)    el.textContent = msg || '';
            if (input) input.classList.toggle('invalid', !!msg);
        }

        function clearErrors() {
            form.querySelectorAll('.error').forEach(function (e) { e.textContent = ''; });
            form.querySelectorAll('.invalid').forEach(function (e) { e.classList.remove('invalid'); });
            if (status) { status.textContent = ''; status.className = 'form-status'; }
        }

        function validate(data) {
            const errors = {};
            if (!data.name    || data.name.trim().length    < 2)  errors.name    = 'Name must be at least 2 characters.';
            if (!data.email   || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) errors.email = 'Enter a valid email address.';
            if (!data.subject || data.subject.trim().length < 3)  errors.subject = 'Subject must be at least 3 characters.';
            if (!data.message || data.message.trim().length < 10) errors.message = 'Message must be at least 10 characters.';
            return errors;
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            clearErrors();

            const fd = new FormData(form);
            const data = {
                name:    fd.get('name'),
                email:   fd.get('email'),
                subject: fd.get('subject'),
                message: fd.get('message'),
            };
            const errors = validate(data);
            if (Object.keys(errors).length) {
                Object.keys(errors).forEach(function (k) { showError(k, errors[k]); });
                if (status) {
                    status.textContent = 'Please fix the highlighted fields.';
                    status.className = 'form-status error';
                }
                return;
            }

            submit.disabled = true;
            submit.textContent = 'Sending…';

            fetch(BASE + '/api/submit_contact.php', {
                method: 'POST',
                body:   fd,
                headers: { 'Accept': 'application/json' },
            })
            .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, body: j }; }); })
            .then(function (res) {
                if (res.ok && res.body.ok) {
                    if (status) {
                        status.textContent = res.body.message || 'Message sent!';
                        status.className = 'form-status success';
                    }
                    form.reset();
                } else if (res.body.errors) {
                    Object.keys(res.body.errors).forEach(function (k) {
                        showError(k, res.body.errors[k]);
                    });
                    if (status) {
                        status.textContent = 'Please correct the errors above.';
                        status.className = 'form-status error';
                    }
                } else {
                    if (status) {
                        status.textContent = res.body.error || 'Something went wrong.';
                        status.className = 'form-status error';
                    }
                }
            })
            .catch(function (err) {
                console.error('Contact submit failed:', err);
                if (status) {
                    status.textContent = 'Network error. Please try again.';
                    status.className = 'form-status error';
                }
            })
            .finally(function () {
                submit.disabled = false;
                submit.textContent = 'Send message';
            });
        });

        // Live-clear errors as the user fixes them.
        form.addEventListener('input', function (e) {
            const target = e.target;
            if (!target.name) return;
            const errorEl = form.querySelector('[data-error-for="' + target.name + '"]');
            if (errorEl) errorEl.textContent = '';
            target.classList.remove('invalid');
        });
    }

    // ---------------- Boot ----------------
    document.addEventListener('DOMContentLoaded', function () {
        initTheme();
        initNav();
        initProjects();
        initContactForm();
    });
})();
