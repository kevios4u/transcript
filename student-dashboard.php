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
        <a href="recipient-info.php">Recipient Information</a>
        <a href="student-profile.php">Student Profile</a>
        <a href="transcript-status.php">Transcript Status</a>
        <a href="print-slip.php">Print Slip</a>
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
          <a class="dashboard-card" id="recipient-info" href="recipient-info.php">
            <ion-icon name="document-text-outline"></ion-icon>
            <div>
              <h3>Recipient Information</h3>
              <p>Add or review the destination details for your transcript.</p>
            </div>
          </a>

          <a class="dashboard-card" id="student-profile" href="student-profile.php">
            <ion-icon name="person-outline"></ion-icon>
            <div>
              <h3>Student Profile</h3>
              <p>Check your personal and academic information.</p>
            </div>
          </a>

          <a class="dashboard-card" id="transcript-status" href="transcript-status.php">
            <ion-icon name="time-outline"></ion-icon>
            <div>
              <h3>Transcript Status</h3>
              <p>View the current state of your transcript application.</p>
            </div>
          </a>

          <a class="dashboard-card" id="print-slip" href="print-slip.php">
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
    <p>Contact us: <ion-icon name="call-outline"></ion-icon> 07012345678  <ion-icon name="mail-outline"></ion-icon> info@yahoo.com</p> 
    <p>Follow us on social media:
      <a href="#"><ion-icon name="logo-facebook"></ion-icon></a> 
      <a href="#"><ion-icon name="logo-twitter"></ion-icon></a> 
      <a href="#"><ion-icon name="logo-instagram"></ion-icon></a>
    </p>
  </footer>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <script src="./assets/js/student-dashboard.js"></script>
</body>
</html>
