import './bootstrap';

const STORAGE_KEYS = {
  theme:       'medrecords_theme',
  sbCollapsed: 'medrecords_sb_collapsed',
};

// ── Dark mode ─────────────────────────────────────────────
window.applyTheme = function(t) {
  document.documentElement.setAttribute('data-theme', t);
  const btn = document.getElementById('darkToggle');
  if (btn) btn.textContent = t === 'dark' ? '🌙' : '☀️';
};

window.toggleDark = function() {
  const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
  localStorage.setItem(STORAGE_KEYS.theme, next);
  window.applyTheme(next);
};

// ── Sidebar ────────────────────────────────────────────────
function toggleMobileSidebar() {
  document.getElementById('sidebar')?.classList.toggle('mobile-open');
  document.getElementById('sbOverlay')?.classList.toggle('mobile-open');
}

window.handleSbBtn = function() {
  if (window.innerWidth <= 900) {
    toggleMobileSidebar();
  } else {
    const app = document.querySelector('.app');
    if (!app) return;
    const collapsed = app.classList.toggle('sb-collapsed');
    localStorage.setItem(STORAGE_KEYS.sbCollapsed, collapsed ? '1' : '0');
    const btn = document.getElementById('sbBtn');
    if (btn) btn.textContent = collapsed ? '›' : '☰';
  }
};

window.toggleSidebar = toggleMobileSidebar;

window.closeSidebar = function() {
  document.getElementById('sidebar')?.classList.remove('mobile-open');
  document.getElementById('sbOverlay')?.classList.remove('mobile-open');
};

// ── User dropdown ──────────────────────────────────────────
window.toggleUserMenu = function() {
  document.getElementById('userDropdown')?.classList.toggle('open');
};

document.addEventListener('click', function(e) {
  const menu = document.getElementById('userMenu');
  if (menu && !menu.contains(e.target)) {
    document.getElementById('userDropdown')?.classList.remove('open');
  }
});

// ── DOMContentLoaded ───────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
  // Auto-dismiss success flash after 5 s
  const flash = document.getElementById('flashMsg');
  if (flash) {
    setTimeout(function() {
      flash.style.transition = 'opacity 0.4s';
      flash.style.opacity    = '0';
      setTimeout(() => flash.remove(), 400);
    }, 5000);
  }

  // Sync dark mode icon with persisted theme
  const darkBtn = document.getElementById('darkToggle');
  if (darkBtn) {
    const t = document.documentElement.getAttribute('data-theme') || 'light';
    darkBtn.textContent = t === 'dark' ? '🌙' : '☀️';
  }

  // Restore collapsed sidebar state (desktop only)
  if (window.innerWidth > 900 && localStorage.getItem(STORAGE_KEYS.sbCollapsed) === '1') {
    const app = document.querySelector('.app');
    if (app) app.classList.add('sb-collapsed');
    const sbBtn = document.getElementById('sbBtn');
    if (sbBtn) sbBtn.textContent = '›';
  }
});
