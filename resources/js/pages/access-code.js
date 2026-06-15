document.addEventListener('DOMContentLoaded', function () {
  const codeDisplay = document.getElementById('code-display');
  const timerEl     = document.getElementById('timer');
  if (!codeDisplay || !timerEl) return;

  const expiryStr = codeDisplay.dataset.expiry;
  if (!expiryStr) return;

  const expiry = new Date(expiryStr);

  function tick() {
    const diff = Math.max(0, Math.floor((expiry - Date.now()) / 1000));
    const m = Math.floor(diff / 60);
    const s = diff % 60;
    timerEl.textContent = 'Valid for ' + m + ':' + String(s).padStart(2, '0');
    if (diff > 0) {
      setTimeout(tick, 1000);
    } else {
      codeDisplay.style.opacity = '0.4';
    }
  }

  tick();
});
