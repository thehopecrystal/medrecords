document.addEventListener('DOMContentLoaded', function () {
  const copyBtn     = document.getElementById('copyMedIdBtn');
  const medIdVal    = document.getElementById('medIdVal');
  const copyConfirm = document.getElementById('copyConfirm');
  if (!copyBtn || !medIdVal) return;

  copyBtn.addEventListener('click', function () {
    const val = medIdVal.textContent.trim();
    navigator.clipboard.writeText(val).then(function () {
      if (copyConfirm) {
        copyConfirm.style.display = 'block';
        setTimeout(function () { copyConfirm.style.display = ''; }, 3000);
      }
    });
  });
});
