// assets/js/main.js
// Handles sidebar collapse, theme toggle, persists preferences

document.addEventListener('DOMContentLoaded', () => {
  const body = document.body;
  const themeKey = 'clinic_theme';
  const sidebarKey = 'clinic_sidebar_collapsed';

  // Elements
  const themeBtns = document.querySelectorAll('.theme-toggle');
  const burger = document.querySelectorAll('.btn-burger');
  const sidebarToggleElements = document.querySelectorAll('[data-toggle="sidebar"]');

  // Init theme
  const savedTheme = localStorage.getItem(themeKey);
  if (savedTheme === 'dark') body.classList.add('dark');

  // Init sidebar collapsed
  const savedCollapsed = localStorage.getItem(sidebarKey);
  if (savedCollapsed === 'true') body.classList.add('sidebar-collapsed');

  // Theme toggle handler
  themeBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      body.classList.toggle('dark');
      localStorage.setItem(themeKey, body.classList.contains('dark') ? 'dark' : 'light');
      themeBtns.forEach(toggle => {
        const icon = toggle.querySelector('i');
        if (icon) icon.className = body.classList.contains('dark') ? 'bi bi-sun' : 'bi bi-moon-stars';
      });
    });
  });

  // Sidebar toggle (burger or explicit toggles)
  const toggleSidebar = () => {
    const collapsed = body.classList.toggle('sidebar-collapsed');
    localStorage.setItem(sidebarKey, collapsed ? 'true' : 'false');
  };

  burger.forEach(b => b.addEventListener('click', toggleSidebar));
  sidebarToggleElements.forEach(el => el.addEventListener('click', toggleSidebar));

  // Accessibility: close sidebar on ESC if in mobile (optional)
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      if (!body.classList.contains('sidebar-collapsed') && window.innerWidth < 900) {
        toggleSidebar();
      }
    }
  });

  // Make nav items show tooltip when collapsed (title attribute)
  document.querySelectorAll('.nav-item').forEach(item => {
    if (!item.querySelector('.label')) return;
    const label = item.querySelector('.label').textContent.trim();
    item.setAttribute('title', label);
  });

});
