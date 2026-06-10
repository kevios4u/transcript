document.addEventListener('DOMContentLoaded', function () {
  const toast = document.getElementById('processToast');
  if (toast) {
    const closeBtn = toast.querySelector('.notification-close');
    if (closeBtn) closeBtn.addEventListener('click', function () { toast.remove(); });
    setTimeout(function () { if (toast && toast.parentNode) toast.parentNode.removeChild(toast); }, 3500);
  }
});
