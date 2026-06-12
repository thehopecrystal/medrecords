import './bootstrap';

// ── Dark mode ─────────────────────────────────────────────
// applyTheme / toggleDark are exposed on window so onclick="" handlers work.
window.applyTheme = function(t) {
  document.documentElement.setAttribute('data-theme', t);
  const btn = document.getElementById('darkToggle');
  if (btn) btn.textContent = t === 'dark' ? '🌙' : '☀️';
};

window.toggleDark = function() {
  const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
  localStorage.setItem('medrecords_theme', next);
  window.applyTheme(next);
};

// ── Sidebar ────────────────────────────────────────────────
window.toggleSidebar = function() {
  document.getElementById('sidebar').classList.toggle('mobile-open');
  document.getElementById('sbOverlay').classList.toggle('mobile-open');
};

window.closeSidebar = function() {
  document.getElementById('sidebar').classList.remove('mobile-open');
  document.getElementById('sbOverlay').classList.remove('mobile-open');
};

// ── User dropdown ──────────────────────────────────────────
window.toggleUserMenu = function() {
  document.getElementById('userDropdown').classList.toggle('open');
};

document.addEventListener('click', function(e) {
  const menu = document.getElementById('userMenu');
  if (menu && !menu.contains(e.target)) {
    const dropdown = document.getElementById('userDropdown');
    if (dropdown) dropdown.classList.remove('open');
  }
});

// ── Flash auto-dismiss ─────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
  const f = document.getElementById('flashMsg');
  if (f) {
    setTimeout(function() {
      f.style.opacity = '0';
      f.style.transition = 'opacity 0.4s';
      setTimeout(() => f.remove(), 400);
    }, 5000);
  }

  // Sync dark mode button icon with current theme
  const btn = document.getElementById('darkToggle');
  if (btn) {
    const t = document.documentElement.getAttribute('data-theme') || 'light';
    btn.textContent = t === 'dark' ? '🌙' : '☀️';
  }
});
