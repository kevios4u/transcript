<?php
  include 'auth-staff.php';

  $staff_name = htmlspecialchars($_SESSION['staff_name'] ?? $_SESSION['username'] ?? 'Staff', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Staff Dashboard: Process Transcript</title>
  <link rel="stylesheet" href="./assets/css/staff-dashboard.css" />
  <link rel="stylesheet" href="./assets/css/staff-process-transcript.css" />
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
        <h2 id="pageTitle">Process Transcript</h2>
        <p>Verify requests, update processing notes, and move transcripts to the next stage.</p>
      </div>

      <div class="staff-grid">
        <article class="staff-panel">
          <div class="panel-heading">
            <ion-icon class="panel-icon" name="construct-outline"></ion-icon>
            <div>
              <h3>Processing Action</h3>
              <p>Select an application and record the next workflow status.</p>
            </div>
          </div>
          <form class="staff-form" action="#" method="post">
            <div class="form-grid">
              <div class="form-group">
                <label for="reg_no">Registration Number</label>
                <input type="text" id="reg_no" name="reg_no">
              </div>
              <div class="form-group">
                <label for="process_status">New Status</label>
                <select id="process_status" name="process_status">
                  <option value="verified">Verified</option>
                  <option value="processing">Processing</option>
                  <option value="approved">Approved</option>
                  <option value="completed">Completed</option>
                </select>
              </div>
              <div class="form-group full-width">
                <label for="process_note">Processing Note</label>
                <textarea id="process_note" name="process_note"></textarea>
              </div>
            </div>
            <button class="staff-button" type="submit">
              <ion-icon name="checkmark-done-outline"></ion-icon>
              Update Request
            </button>
          </form>
        </article>

        <article class="staff-panel">
          <div class="panel-heading">
            <ion-icon class="panel-icon" name="list-outline"></ion-icon>
            <div>
              <h3>Pending Queue</h3>
              <p>Requests waiting for staff review will appear here.</p>
            </div>
          </div>
          <div class="empty-state">
            <ion-icon name="time-outline"></ion-icon>
            <h3>No Pending Requests</h3>
            <p>Connect this panel to pending applications when the processing query is ready.</p>
          </div>
        </article>
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
  <script src="./assets/js/staff-process-transcript.js"></script>
</body>
</html>
