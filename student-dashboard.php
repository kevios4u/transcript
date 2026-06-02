<!-- prepare student dashboard with menu bar list of recipients information, application status, print slip, application history, and logout button, welcome message with student name, footer with contact information and social media links, and a responsive design for mobile devices. -->
 <?php
  include 'auth-student.php';

  // Get the student's name from the session
  $student_name = htmlspecialchars($_SESSION['student_name'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Dashboard</title>
  <link rel="stylesheet" href="./assets/css/student-dashboard.css" />
</head>
<body>
    <header class="dashboard-header">
      <h1><ion-icon name="person"></ion-icon> Welcome,<span><?php echo $student_name; ?>!</span></h1>
      <nav class="dashboard-nav" id="dashboardNav">
        <a href="#recipient-info">Recipient Information</a>
        <a href="#student-profile">Student Profile</a>
        <a href="#transcript-status">Transcript Status</a>
        <a href="#print-slip">Print Slip</a>
        <a href="student_logout.php">Logout</a>
      </nav>
      <div class="dashboard-menu" role="button" tabindex="0" aria-label="Open menu" aria-controls="dashboardNav" aria-expanded="false">
        <ion-icon name="menu"></ion-icon>
      </div>
    </header>

    <main class="dashboard-main">
      <section class="dashboard-overview" aria-labelledby="overviewTitle">
        <div class="overview-heading">
          <h2 id="overviewTitle">Dashboard Overview</h2>
          <p>Manage your transcript request and track your application progress.</p>
        </div>

        <div class="dashboard-cards">
          <a class="dashboard-card" id="recipient-info" href="#">
            <ion-icon name="document-text-outline"></ion-icon>
            <div>
              <h3>Recipient Information</h3>
              <p>Add or review the destination details for your transcript.</p>
            </div>
          </a>

          <a class="dashboard-card" id="student-profile" href="#">
            <ion-icon name="person-outline"></ion-icon>
            <div>
              <h3>Student Profile</h3>
              <p>Check your personal and academic information.</p>
            </div>
          </a>

          <a class="dashboard-card" id="transcript-status" href="#">
            <ion-icon name="time-outline"></ion-icon>
            <div>
              <h3>Transcript Status</h3>
              <p>View the current state of your transcript application.</p>
            </div>
          </a>

          <a class="dashboard-card" id="print-slip" href="#">
            <ion-icon name="print-outline"></ion-icon>
            <div>
              <h3>Print Slip</h3>
              <p>Print or download your application slip when it is ready.</p>
            </div>
          </a>
        </div>
      </section>
    </main>
  <footer class="dashboard-footer">
    <p>Contact us:07012345678 | Email:info@yahoo.com</p>
    <p>Follow us on social media:
      <a href="#">Facebook</a> |
      <a href="#">Twitter</a> |
      <a href="#">Instagram</a>
    </p>
  </footer>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script>
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
</script>
</body>
</html>
