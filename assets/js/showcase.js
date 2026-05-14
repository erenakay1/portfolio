/* =============================================================
 * Interactive JS Demos — BOM, DOM, Events, Timers.
 * Refactored from Week 7 lab demos so they can live in a single
 * collapsible "Showcase" section on the homepage.
 * ============================================================= */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        // ----- BOM info -----
        const url  = document.getElementById('bom-url');
        const lang = document.getElementById('bom-lang');
        const res  = document.getElementById('bom-res');
        if (url)  url.textContent  = window.location.href;
        if (lang) lang.textContent = navigator.language;
        if (res)  res.textContent  = screen.width + ' x ' + screen.height;

        // ----- Digital clock (setInterval) -----
        const clock = document.getElementById('digital-clock');
        if (clock) {
            const tick = function () { clock.textContent = new Date().toLocaleTimeString(); };
            tick();
            setInterval(tick, 1000);
        }

        // ----- Event delegation -----
        const list   = document.getElementById('delegation-list');
        const result = document.getElementById('delegation-result');
        if (list && result) {
            list.addEventListener('click', function (e) {
                if (e.target.tagName === 'LI') {
                    result.textContent = 'Clicked: ' + e.target.textContent.trim();
                }
            });
        }

        // ----- Keyboard tracker -----
        const keyDisplay = document.getElementById('last-key');
        if (keyDisplay) {
            document.addEventListener('keydown', function (e) {
                // Ignore key events while the user is typing in a form field.
                const tag = (e.target && e.target.tagName) || '';
                if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT') return;
                keyDisplay.textContent = e.key === ' ' ? 'Space' : e.key;
            });
        }
    });
})();
