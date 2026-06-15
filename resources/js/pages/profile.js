document.addEventListener('DOMContentLoaded', function () {
  var TABS = ['personal', 'medical', 'emergency', 'security', 'two-factor'];
  var tabs = Array.from(document.querySelectorAll('.profile-tab[data-tab]'));

  if (!tabs.length) return;

  function activate(tabId) {
    if (TABS.indexOf(tabId) === -1) tabId = 'personal';

    tabs.forEach(function (t) {
      var active = t.dataset.tab === tabId;
      t.classList.toggle('active', active);
      t.setAttribute('aria-selected', active ? 'true' : 'false');
    });

    TABS.forEach(function (id) {
      var panel = document.getElementById('tab-' + id);
      if (panel) panel.classList.toggle('active', id === tabId);
    });

    try { localStorage.setItem('profileActiveTab', tabId); } catch (_) {}
  }

  function getInitialTab() {
    // Validation errors take priority — auto-open whichever tab has errors
    for (var i = 0; i < TABS.length; i++) {
      var panel = document.getElementById('tab-' + TABS[i]);
      if (panel && panel.querySelector('.form-error')) return TABS[i];
    }
    // URL hash (#personal, #security, etc.)
    var hash = location.hash.replace('#', '');
    if (TABS.indexOf(hash) !== -1) return hash;
    // Persist across page reloads / successful saves
    try {
      var saved = localStorage.getItem('profileActiveTab');
      if (TABS.indexOf(saved) !== -1) return saved;
    } catch (_) {}
    return 'personal';
  }

  // Tab click
  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      var id = this.dataset.tab;
      activate(id);
      history.replaceState(null, '', '#' + id);
    });
  });

  // Arrow-key navigation within the tab bar (ARIA pattern)
  tabs.forEach(function (tab, i) {
    tab.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowRight') {
        tabs[(i + 1) % tabs.length].focus();
      } else if (e.key === 'ArrowLeft') {
        tabs[(i - 1 + tabs.length) % tabs.length].focus();
      }
    });
  });

  // data-go-tab links (e.g. "Change number" in the 2FA panel)
  document.querySelectorAll('[data-go-tab]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      e.preventDefault();
      var id = this.dataset.goTab;
      activate(id);
      history.replaceState(null, '', '#' + id);
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  });

  activate(getInitialTab());
});
