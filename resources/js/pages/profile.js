document.addEventListener('DOMContentLoaded', function () {
  const sections = ['personal', 'medical', 'emergency', 'security', 'two-factor'];
  const pairs = sections.flatMap(id => {
    const section = document.getElementById(id);
    const link    = document.querySelector(`.profile-nav-item[href="#${id}"]`);
    return section && link ? [{ section, link }] : [];
  });

  if (!pairs.length) return;

  function activate(link) {
    pairs.forEach(p => p.link.classList.remove('active'));
    link.classList.add('active');
  }

  function update() {
    const topbar = document.querySelector('.topbar');
    const offset = (topbar ? topbar.offsetHeight : 62) + 40;
    const active = pairs.slice().reverse().find(p => {
      return p.section.getBoundingClientRect().top <= offset;
    });
    if (active) activate(active.link);
  }

  window.addEventListener('scroll', update, { passive: true });
  update();
});
