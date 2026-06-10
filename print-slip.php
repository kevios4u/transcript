<?php

  include 'dbConnect.php';
  include 'auth-student.php';

  // Get the student's name from the session
  $student_name = htmlspecialchars($_SESSION['student_name'], ENT_QUOTES, 'UTF-8');
  $reg_no = htmlspecialchars($_SESSION['reg_no'], ENT_QUOTES, 'UTF-8');

  // Fetch student profile
          $profile = [];
          $stmt = $conn->prepare("SELECT student_name, reg_no, gender, email, phone_no, department, programme, level, course_study FROM student_profile WHERE reg_no = ? LIMIT 1");
          $stmt->bind_param("s", $_SESSION['reg_no']);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result && $result->num_rows > 0) {
            $profile = $result->fetch_assoc();
          }
          $stmt->close();

          // Load the requested transcript slip record if set, otherwise fall back to latest status
          $request_id = isset($_GET['request_id']) ? intval($_GET['request_id']) : null;
          $status = null;
          if ($request_id) {
            $stmt2 = $conn->prepare("SELECT p.progress_status, p.progress_note, p.recipient_id, COALESCE(r.institution_name, '-') AS institution_name, COALESCE(r.recipient_name, '-') AS recipient_name, p.process_status_id FROM process_status p LEFT JOIN recipient r ON p.recipient_id = r.recipient_id WHERE p.process_status_id = ? AND p.reg_no = ? LIMIT 1");
            $stmt2->bind_param("is", $request_id, $_SESSION['reg_no']);
            $stmt2->execute();
            $res2 = $stmt2->get_result();

            if ($res2 && $res2->num_rows > 0) {
              $status = $res2->fetch_assoc();
            }
            $stmt2->close();
          }

          if (!$status) {
            $stmt2 = $conn->prepare("SELECT p.progress_status, p.progress_note, p.recipient_id, COALESCE(r.institution_name, '-') AS institution_name, COALESCE(r.recipient_name, '-') AS recipient_name, p.process_status_id FROM process_status p LEFT JOIN recipient r ON p.recipient_id = r.recipient_id WHERE p.reg_no = ? ORDER BY p.process_status_id DESC LIMIT 1");
            $stmt2->bind_param("s", $_SESSION['reg_no']);
            $stmt2->execute();
            $res2 = $stmt2->get_result();
            if ($res2 && $res2->num_rows > 0) {
              $status = $res2->fetch_assoc();
            }
            $stmt2->close();
          }
          $conn->close();

          function esc($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
  ?>
  <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Dashboard: Print Slip</title>
  <link rel="stylesheet" href="./assets/css/student-dashboard.css" />
  <link rel="stylesheet" href="./assets/css/print-slip.css" />
</head>
<body>
    <header class="dashboard-header">
      <div class="header-logo-container">
        <img src="./assets/images/nilest-logo.png" alt="NILEST Logo" class="header-logo">
        <span class="header-school-name">Nigerian Institute of Leather and Science Technology, Zaria</span>
      </div>
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

    <main class="print-slip-main">
      <div class="dashboard-welcome">
        <h1>Welcome, <span><?php echo $student_name; ?></span>!</h1>
      </div>
      <section class="print-slip-overview" aria-labelledby="overviewTitle">
        <div class="overview-heading">
          <h2 id="overviewTitle">Transcript Print Slip</h2>
          <p>View your print slip details.</p>
        </div>

        <div class="slip-actions">
          <button id="printBtn" class="form-submit">Print</button>
          <button id="downloadPdfBtn" class="form-submit">Download PDF</button>
        </div>

        <div id="slip" class="slip-card">
          <div class="slip-header">
            <img src="./assets/images/nilest-logo.png" alt="Institute logo" class="slip-logo">
            <div class="institute-name">Nigerian Institute of Leather and Science Technology, Zaria</div>
          </div>
          <h3 class="slip-title">Transcript Print Slip</h3>
          <dl class="slip-details">
            <div>
              <dt>Student Name</dt>
              <dd><?php echo esc($profile['student_name'] ?? $student_name); ?></dd>
            </div>
            <div>
              <dt>Registration Number</dt>
              <dd><?php echo esc($profile['reg_no'] ?? $reg_no); ?></dd>
            </div>
            <div>
              <dt>Course of Study</dt>
              <dd><?php echo esc($profile['course_study'] ?? ''); ?></dd>
            </div>
            <div>
              <dt>Programme</dt>
              <dd><?php echo esc($profile['programme'] ?? ''); ?></dd>
            </div>
            <div>
              <dt>Level</dt>
              <dd><?php echo esc($profile['level'] ?? ''); ?></dd>
            </div>
            <div>
              <dt>Current Status</dt>
              <dd><?php echo esc($status['progress_status'] ?? 'Not processed'); ?></dd>
            </div>
            <div>
              <dt>Recipient Name</dt>
              <dd><?php echo esc($status['recipient_name'] ?? '-'); ?></dd>
            </div>
            <div>
              <dt>Recipient Institution</dt>
              <dd><?php echo esc($status['institution_name'] ?? '-'); ?></dd>
            </div>
          </dl>
        </div>

      </section>
    </main>
        <footer class="dashboard-footer">
    <p>Contact us: <ion-icon name="call-outline"></ion-icon> 07012345678  <ion-icon name="mail-outline"></ion-icon> info@yahoo.com</p>
    <p>Follow us on social media:
      <a href="#"><ion-icon name="logo-facebook"></ion-icon></a>
      <a href="#"><ion-icon name="logo-twitter"></ion-icon></a>
      <a href="#"><ion-icon name="logo-instagram"></ion-icon></a>
      <a href="#"><ion-icon name="logo-whatsapp"></ion-icon></a>
      <a href="#"><ion-icon name="logo-google"></ion-icon></a>
      <a href="#"><ion-icon name="logo-linkedin"></ion-icon></a>
    </p>
  </footer>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="./assets/js/student-dashboard.js"></script>
    <script src="./assets/js/print-slip.js"></script>
</body>
</html>
