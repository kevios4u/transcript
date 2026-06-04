<?php
  include 'auth-staff.php';

  $staff_name = htmlspecialchars($_SESSION['staff_name'] ?? $_SESSION['username'] ?? 'Staff', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Staff Dashboard</title>
  <link rel="stylesheet" href="./assets/css/staff-dashboard.css" />
</head>
<body>
  <header class="dashboard-header">
    <div class="header-logo-container">
      <img src="./assets/images/nilest-logo.png" alt="NILEST Logo" class="header-logo">
      <span class="header-school-name">Nigerian Institute of Leather and Science Technology, Zaria</span>
    </div>
    <nav class="dashboard-nav" id="dashboardNav">
      <a href="staff-update-student-profile.php">Update Students</a>
      <a href="staff-view-application.php">View Applications</a>
      <a href="staff-process-transcript.php">Process Transcript</a>
      <a href="staff-view-transcript.php">View Transcripts</a>
      <a href="staff_logout.php">Logout</a>
    </nav>
    <div class="dashboard-menu" role="button" tabindex="0" aria-label="Open menu" aria-controls="dashboardNav" aria-expanded="false">
      <ion-icon name="menu"></ion-icon>
    </div>
  </header>

  <main class="dashboard-main">
    <section class="dashboard-overview" aria-labelledby="overviewTitle">
      <div class="dashboard-welcome">
        <div>
          <p class="dashboard-kicker">Staff Workspace</p>
          <h1 id="overviewTitle">Welcome, <span><?php echo $staff_name; ?></span>!</h1>
          <p>Manage student records, transcript applications, processing queues, and completed transcripts.</p>
        </div>
      </div>

      <div class="dashboard-cards">
        <a class="dashboard-card" href="staff-update-student-profile.php">
          <ion-icon name="cloud-upload-outline"></ion-icon>
          <div>
            <h3>Update Students</h3>
            <p>Upload student CSV records or add a new student profile manually.</p>
          </div>
        </a>

        <a class="dashboard-card" href="staff-view-application.php">
          <ion-icon name="folder-open-outline"></ion-icon>
          <div>
            <h3>View Applications</h3>
            <p>Review submitted transcript applications and recipient details.</p>
          </div>
        </a>

        <a class="dashboard-card" href="staff-process-transcript.php">
          <ion-icon name="construct-outline"></ion-icon>
          <div>
            <h3>Process Transcript</h3>
            <p>Move transcript requests through verification, processing, and approval.</p>
          </div>
        </a>

        <a class="dashboard-card" href="staff-view-transcript.php">
          <ion-icon name="document-attach-outline"></ion-icon>
          <div>
            <h3>View Transcripts</h3>
            <p>Browse processed transcripts and confirm completed request records.</p>
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
  <script src="./assets/js/staff-dashboard.js"></script>
</body>
</html>
