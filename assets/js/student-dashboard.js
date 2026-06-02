 const menuButton = document.querySelector('.dashboard-menu');
  const dashboardNav = document.querySelector('#dashboardNav');
  const menuIcon = menuButton.querySelector('ion-icon');

  menuButton.addEventListener('click', () => {
    const isOpen = dashboardNav.classList.toggle('is-open');

    menuButton.setAttribute('aria-expanded', isOpen);
    menuButton.setAttribute('aria-label', isOpen ? 'Close menu' : 'Open menu');
    menuIcon.setAttribute('name', isOpen ? 'close' : 'menu');
  });

  menuButton.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();
      menuButton.click();
    }
  });

  dashboardNav.addEventListener('click', (event) => {
    if (event.target.tagName !== 'A') {
      return;
    }

    dashboardNav.classList.remove('is-open');
    menuButton.setAttribute('aria-expanded', 'false');
    menuButton.setAttribute('aria-label', 'Open menu');
    menuIcon.setAttribute('name', 'menu');
  });