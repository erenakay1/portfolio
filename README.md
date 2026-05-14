# Eren Akay — Full-Stack Portfolio

A personal portfolio site built end-to-end with vanilla PHP, MySQL, and vanilla
JavaScript. Submitted as the Web Programming final project for the 2025–2026
academic year at Haliç University.

The site presents the author's profile and projects on a single public page,
loads project data asynchronously from a MySQL database via the Fetch API, and
exposes a protected admin dashboard for managing projects and contact messages.

---

## Tech stack

- **Frontend:** HTML5 (semantic), CSS3 (Flexbox + Grid, CSS variables, glassmorphism), vanilla JavaScript (no jQuery, no framework)
- **Backend:** PHP 8.x (no Composer / no framework), PDO with prepared statements
- **Database:** MySQL 5.7+/8.x
- **State & Auth:** PHP sessions + cookies, `password_hash` / `password_verify`, CSRF tokens
- **AJAX:** Fetch API for projects and contact form
- **Deployment:** InfinityFree-compatible (no proc_open, no build step)

---

## Features mapped to requirements

| Requirement | Implementation |
|-------------|----------------|
| Semantic HTML5 | `<nav>`, `<main>`, `<section>`, `<article>`, `<footer>` in `index.php` |
| Responsive CSS (Flex/Grid) | `assets/css/style.css` — breakpoints at 900/700/600px |
| External stylesheet + consistent branding | Single `style.css`, CSS variables, glass aesthetic |
| Dark mode toggle | `#theme-toggle` in nav; persisted in `localStorage` + cookie |
| Image slider / fade-in animation | Fade-up animation on project cards |
| Interactive menus | Sticky nav + collapsible "JS Demos" section + project detail modal |
| JS form validation before submission | Client-side validation in `assets/js/main.js` (contact form) |
| DOM manipulation on events | Project rendering, modal, mobile-nav toggle, showcase demos |
| PHP + MySQL contact form | `api/submit_contact.php` → `contact_messages` table |
| Projects from MySQL rendered on page | `api/get_projects.php` returns JSON; rendered by `main.js` |
| AJAX (Fetch API) | Projects fetch + contact form submission — no full reloads |
| Sessions + cookies admin login | `admin/login.php` + `includes/auth.php` |
| Protected admin dashboard | `require_admin()` guard on every admin page |
| CRUD projects | `admin/project_add.php`, `_edit.php`, `_delete.php` |
| Secure login | Prepared statements, `password_verify`, CSRF, `session_regenerate_id(true)` |

---

## Folder structure

```
portfolio/
├── index.php                  # Public homepage
├── config.example.php         # Template for DB credentials
├── config.php                 # Local credentials (not for production)
├── .htaccess                  # Minimal hardening
├── admin/
│   ├── login.php
│   ├── logout.php
│   ├── dashboard.php
│   ├── project_add.php
│   ├── project_edit.php
│   ├── project_delete.php
│   └── _project_form_fields.php   # Shared form partial
├── api/
│   ├── get_projects.php       # JSON list (optional ?category filter)
│   └── submit_contact.php     # POST contact form, returns JSON
├── includes/
│   ├── db.php                 # PDO singleton
│   ├── auth.php               # Session bootstrap + admin helpers
│   ├── csrf.php               # CSRF token generation/verification
│   ├── header.php             # Shared <head> + top nav
│   └── footer.php             # Shared footer + script tags
├── assets/
│   ├── css/style.css
│   └── js/
│       ├── main.js            # Portfolio frontend + AJAX
│       └── showcase.js        # Week-7 BOM/DOM/Events demos
├── sql/portfolio.sql          # Schema + seed data + admin user
└── README.md                  # This file
```

---

## Setup — Local (XAMPP)

1. Copy the `portfolio/` directory into `htdocs/` (so it lives at `htdocs/portfolio`).
2. Start Apache and MySQL from the XAMPP control panel.
3. Open <http://localhost/phpmyadmin> and create a database called `portfolio`
   (utf8mb4 / utf8mb4_unicode_ci).
4. Select that database, click **Import**, upload `sql/portfolio.sql`, then **Go**.
5. Open `config.php` and confirm the local credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'portfolio');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```
6. Visit <http://localhost/portfolio>.
7. Visit <http://localhost/portfolio/admin/login.php> and sign in with:
   - **Username:** `admin`
   - **Password:** `Portfolio2026!`

---

## Setup — InfinityFree

1. Create a new MySQL database from the InfinityFree control panel and note the
   host / database name / username / password it gives you.
2. In phpMyAdmin (linked from the control panel), import `sql/portfolio.sql`
   into that database.
3. Open `config.php` and replace the local credentials with the ones InfinityFree
   provided:
   ```php
   define('DB_HOST', 'sqlXYZ.infinityfree.com');
   define('DB_NAME', 'epiz_xxxxx_portfolio');
   define('DB_USER', 'epiz_xxxxx');
   define('DB_PASS', 'your-db-password');
   define('SITE_URL', 'https://your-site.infinityfreeapp.com');
   ```
4. Upload the entire `portfolio/` directory to `htdocs/` on the FTP host (so the
   files live directly under `htdocs/`, not `htdocs/portfolio`).
5. Visit your InfinityFree domain. The homepage should load projects via AJAX
   immediately.
6. Visit `/admin/login.php` with username `admin` / password `Portfolio2026!`.
   **Change the password immediately** by generating a new hash and updating the
   `admin_users` row in phpMyAdmin.

> InfinityFree restricts certain PHP/Apache directives. The shipped `.htaccess`
> uses only safe rules: `DirectoryIndex`, `FilesMatch`, `Options -Indexes`, basic
> `mod_headers` and `mod_expires`. No URL rewriting is required.

---

## Security notes

- All SQL goes through PDO prepared statements — no string concatenation.
- The admin password is stored as a bcrypt hash (`password_hash`) and verified
  with `password_verify`.
- `session_regenerate_id(true)` is called after a successful login to defeat
  session fixation.
- Every state-changing form (login, contact, project CRUD) carries a CSRF token
  generated from `random_bytes(32)`; verification uses `hash_equals`.
- All user-controlled output is escaped with `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
- Session cookies use `httponly` and `SameSite=Lax`; `secure` is enabled when
  HTTPS is detected.
- The contact form has a honeypot field and server-side validation on top of
  client-side checks.

---

## Screenshots

_Placeholders — replace with real screenshots before submitting._

- `docs/screenshot-hero.png` — Hero + nav (light mode)
- `docs/screenshot-hero-dark.png` — Hero + nav (dark mode)
- `docs/screenshot-projects.png` — Projects grid with category filter
- `docs/screenshot-modal.png` — Project detail modal
- `docs/screenshot-contact.png` — Contact form with validation
- `docs/screenshot-dashboard.png` — Admin dashboard
- `docs/screenshot-edit.png` — Admin edit project

---

## Live demo

_Add the URL here after InfinityFree deployment._

- Public site: `https://<your-subdomain>.infinityfreeapp.com/`
- Admin login: `https://<your-subdomain>.infinityfreeapp.com/admin/login.php`

---

## Author

**Eren Akay**
3rd-year Software Engineering student at Haliç University
AI Engineering Intern @ UtaiSOFT · Board member at HUGİP
GitHub: [github.com/erenakay1](https://github.com/erenakay1)
Location: Istanbul, Turkey
