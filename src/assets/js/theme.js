/** TARAZINO | Shared light/dark theme control. */
(function (w, d) {
    'use strict';

    const toggles = Array.from(d.querySelectorAll('[data-theme-toggle]'));
    const current = d.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';

    function selectTheme(theme) {
        d.documentElement.setAttribute('data-bs-theme', theme);
        toggles.forEach(toggle => { toggle.checked = theme === 'dark'; });
        try { w.localStorage.setItem('tarazino-theme', theme); }
        catch (_) { /* Storage can be unavailable. */ }
    }

    toggles.forEach(toggle => {
        toggle.checked = current === 'dark';
        toggle.addEventListener('change', () => {
            selectTheme(toggle.checked ? 'dark' : 'light');
        });
    });
})(window, document);
