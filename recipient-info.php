 <?php
  include 'auth-student.php';

  // Get the student's name from the session
  $student_name = htmlspecialchars($_SESSION['student_name'], ENT_QUOTES, 'UTF-8');
  $reg_no = htmlspecialchars($_SESSION['reg_no'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Dashboard: Recipient Information</title>
  <link rel="stylesheet" href="./assets/css/student-dashboard.css" />
  <link rel="stylesheet" href="./assets/css/recipient-info.css" />
</head>
<body>
    <header class="dashboard-header">
      <h1><ion-icon name="person"></ion-icon> Welcome,<span><?php echo $student_name; ?>!</span></h1>
      <nav class="dashboard-nav" id="dashboardNav">
        <a href="student-dashboard.php">Dashboard</a>
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

    <main class="recipient-main">
      <section class="recipient-overview" aria-labelledby="overviewTitle">
        <div class="overview-heading">
          <h2 id="overviewTitle">Recipient Information</h2>
          <p>Fill in the details of the institution, which you want to send your transcript.</p>
        </div>

        <form class="recipient-form" action="#" method="post">
          <input type="hidden" name="reg_no" value="<?php echo $reg_no; ?>">

          <div class="form-grid">
            <div class="form-group">
              <label for="recipient_name">Recipient Name</label>
              <input type="text" id="recipient_name" name="recipient_name" placeholder="Recipient name" required>
            </div>

            <div class="form-group">
              <label for="institution_name">Institution Name</label>
              <input type="text" id="institution_name" name="institution_name" placeholder="Name of institution" required>
            </div>
          </div>

          <div class="form-group form-wide">
            <label for="institution_address">Institution Address</label>
            <textarea id="institution_address" name="institution_address" rows="4" placeholder="Full institution address" required></textarea>
          </div>

          <div class="form-actions">
            <button type="submit" class="form-submit">Submit Recipient Info</button>
            <button type="reset" class="form-reset">Clear Form</button>
          </div>
        </form>
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
