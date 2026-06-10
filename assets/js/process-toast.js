document.addEventListener('DOMContentLoaded', function () {
  const forms = document.querySelectorAll('form.process-form');
  function showToast(message, type = 'success') {
    // remove existing toast if present
    const existing = document.getElementById('processClickToast');
    if (existing && existing.parentNode) existing.parentNode.removeChild(existing);

    const toast = document.createElement('div');
    toast.id = 'processClickToast';
    toast.className = 'notification-toast ' + (type === 'error' ? 'error' : 'success');
    toast.style.opacity = '0';
    toast.style.transition = 'opacity 0.2s, transform 0.2s';
    toast.innerHTML = '<p>' + message + '</p>';
    document.body.appendChild(toast);
    // force reflow
    void toast.offsetWidth;
    toast.style.opacity = '1';
    toast.style.transform = 'translateY(0)';

    setTimeout(function () {
      if (toast && toast.parentNode) toast.parentNode.removeChild(toast);
    }, 3000);
  }

  if (!forms || forms.length === 0) return;

  forms.forEach(function (form) {
    form.addEventListener('submit', function () {
      showToast('Processing application...', 'success');
      // allow normal submit to proceed
    });
  });
});
