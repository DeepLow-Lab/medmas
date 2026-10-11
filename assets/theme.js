// Legacy entry point kept for pages that still reference theme.js.
document.addEventListener('DOMContentLoaded', function () {
    var body = document.body;
    if (localStorage.getItem('clinic_theme') === 'dark') {
        body.classList.add('dark');
    }
});
