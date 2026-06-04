<?php
  include 'auth-staff.php';

  $staff_name = htmlspecialchars($_SESSION['staff_name'] ?? $_SESSION['username'] ?? 'Staff', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Staff Dashboard: View Transcripts</title>
  <link rel="stylesheet" href="./assets/css/staff-dashboard.css" />
  <link rel="stylesheet" href="./assets/css/staff-view-transcript.css" />
</head>
<body>
  <header class="dashboard-header">
    <div class="header-logo-container">
      <img src="./assets/images/nilest-logo.png" alt="NILEST Logo" class="header-logo">
      <span class="header-school-name">Nigerian Institute of Leather and Science Technology, Zaria</span>
    </div>
    <nav class="dashboard-nav" id="dashboardNav">
      <a href="staff-dashboard.php">Dashboard</a>
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

  <main class="staff-main">
    <div class="dashboard-welcome">
      <h1>Welcome, <span><?php echo $staff_name; ?></span>!</h1>
    </div>

    <section class="staff-section" aria-labelledby="pageTitle">
      <div class="overview-heading">
        <h2 id="pageTitle">Processed Transcripts</h2>
        <p>View completed transcript records and prepare them for release or archiving.</p>
      </div>

      <article class="staff-panel">
        <form class="staff-form" action="#" method="get">
          <div class="filter-row">
            <div class="form-group">
              <label for="search">Search</label>
              <input type="search" id="search" name="search" placeholder="Reg no, name, or transcript ID">
            </div>
            <div class="form-group">
              <label for="from_date">From</label>
              <input type="date" id="from_date" name="from_date">
            </div>
            <div class="form-group">
              <label for="to_date">To</label>
              <input type="date" id="to_date" name="to_date">
            </div>
            <button class="staff-button" type="submit">
              <ion-icon name="search-outline"></ion-icon>
              Filter
            </button>
          </div>
        </form>

        <div class="table-wrap">
          <table class="staff-table">
            <thead>
              <tr>
                <th>Transcript ID</th>
                <th>Student</th>
                <th>Reg No</th>
                <th>Completed Date</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="6">
                  <div class="empty-state">
                    <ion-icon name="document-attach-outline"></ion-icon>
                    <h3>No Processed Transcripts</h3>
                    <p>Completed transcript records will appear here after processing is connected.</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </article>
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
  <script src="./assets/js/staff-view-transcript.js"></script>
</body>
</html>
