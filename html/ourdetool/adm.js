/* DE Admintool – minimales Vanilla-JS (kein jQuery nötig). */
(function () {
    'use strict';

    // Bestätigungsdialoge über data-confirm auf Links und Formularen
    document.addEventListener('click', function (e) {
        var el = e.target.closest('a[data-confirm]');
        if (el && !window.confirm(el.getAttribute('data-confirm'))) {
            e.preventDefault();
        }
    });

    document.addEventListener('submit', function (e) {
        var el = e.target.closest('form[data-confirm]');
        if (el && !window.confirm(el.getAttribute('data-confirm'))) {
            e.preventDefault();
        }
    });
}());
