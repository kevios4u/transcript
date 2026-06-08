document.addEventListener('DOMContentLoaded', function () {
  const dropForms = document.querySelectorAll('form.inline-form');
  const overlay = document.getElementById('dropConfirmOverlay');
  const confirmDrop = document.getElementById('confirmDrop');
  const confirmCancel = document.getElementById('confirmCancel');
  const toast = document.getElementById('recipientToast');
  let activeForm = null;

  function openModal() {
    overlay.classList.add('active');
    overlay.setAttribute('aria-hidden', 'false');
    confirmCancel.focus();
  }

  function closeModal() {
    overlay.classList.remove('active');
    overlay.setAttribute('aria-hidden', 'true');
    activeForm = null;
  }

  function hideToast() {
    if (toast) {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(-10px)';
      window.setTimeout(function () {
        if (toast && toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 250);
    }
  }

  dropForms.forEach(function (form) {
    form.addEventListener('submit', function (event) {
      event.preventDefault();
      activeForm = form;
      openModal();
    });
  });

  if (toast) {
    const closeButton = toast.querySelector('.notification-close');
    closeButton.addEventListener('click', hideToast);
    window.setTimeout(hideToast, 5000);
  }

  confirmDrop.addEventListener('click', function () {
    if (activeForm) {
      activeForm.submit();
    }
  });

  confirmCancel.addEventListener('click', closeModal);
  overlay.addEventListener('click', function (event) {
    if (event.target === overlay) {
      closeModal();
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && overlay.classList.contains('active')) {
      closeModal();
    }
  });
});
