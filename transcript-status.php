<?php

  include 'dbConnect.php';
  include 'auth-student.php';

  // Get the student's name from the session
  $student_name = htmlspecialchars($_SESSION['student_name'], ENT_QUOTES, 'UTF-8');
  $reg_no = htmlspecialchars($_SESSION['reg_no'], ENT_QUOTES, 'UTF-8');
  $transcript_status = null;

  $stmt = $conn->prepare("SELECT progress_status FROM process_status WHERE reg_no = ? ORDER BY process_status_id DESC LIMIT 1");
  $stmt->bind_param("s", $_SESSION['reg_no']);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $transcript_status = $result->fetch_assoc();
  }

  $stmt->close();
  $conn->close();

  function transcript_status_feedback($status) {
    if ($status === null || trim((string) $status) === '') {
      return [
        'class' => 'status-empty',
        'icon' => 'document-text-outline',
        'label' => 'No transcript processing record found',
        'message' => 'Your transcript has not been processed yet. Please check back later.'
      ];
    }

    $clean_status = trim((string) $status);
    $normalized_status = strtolower($clean_status);

    if (strpos($normalized_status, 'processed') !== false || strpos($normalized_status, 'completed') !== false || strpos($normalized_status, 'ready') !== false || strpos($normalized_status, 'approved') !== false) {
      return [
        'class' => 'status-processed',
        'icon' => 'checkmark-circle-outline',
        'label' => 'Transcript processed',
        'message' => 'Your transcript has been processed successfully.'
      ];
    }

    return [
      'class' => 'status-pending',
      'icon' => 'time-outline',
      'label' => 'Transcript not yet processed',
      'message' => 'Your transcript request is still in progress.'
    ];
  }

  $status_text = $transcript_status['progress_status'] ?? null;
  $status_feedback = transcript_status_feedback($status_text);
  $status_display = htmlspecialchars($status_text ?? 'Not processed', ENT_QUOTES, 'UTF-8');

  ?>
  <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Dashboard: Transcript Status</title>
  <link rel="stylesheet" href="./assets/css/student-dashboard.css" />
  <link rel="stylesheet" href="./assets/css/transcript-status.css" />
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

    <main class="transcript-main">
      <div class="dashboard-welcome">
        <h1>Welcome, <span><?php echo $student_name; ?></span>!</h1>
      </div>
      <section class="transcript-overview" aria-labelledby="overviewTitle">
        <div class="overview-heading">
          <h2 id="overviewTitle">Transcript Status</h2>
          <p>View your transcript status.</p>
        </div>

        <div class="status-card <?php echo $status_feedback['class']; ?>">
          <ion-icon name="<?php echo $status_feedback['icon']; ?>" aria-hidden="true"></ion-icon>
          <div class="status-content">
            <p class="status-label"><?php echo $status_feedback['label']; ?></p>
            <p class="status-message"><?php echo $status_feedback['message']; ?></p>
            <dl class="status-details">
              <div>
                <dt>Registration Number</dt>
                <dd><?php echo $reg_no; ?></dd>
              </div>
              <div>
                <dt>Current Status</dt>
                <dd><?php echo $status_display; ?></dd>
              </div>
            </dl>
          </div>
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
  <script src="./assets/js/transcript-status.js"></script>
</body>
</html>
