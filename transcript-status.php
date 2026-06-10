<?php

  include 'dbConnect.php';
  include 'auth-student.php';

  // Get the student's name from the session
  $student_name = htmlspecialchars($_SESSION['student_name'], ENT_QUOTES, 'UTF-8');
  $reg_no = htmlspecialchars($_SESSION['reg_no'], ENT_QUOTES, 'UTF-8');
  $transcript_status = null;
  $all_statuses = [];

  // Fetch all process_status rows for this student (most recent first)
  if ($stmt = $conn->prepare("SELECT p.process_status_id, p.recipient_id, p.reg_no, p.progress_status, p.progress_note, COALESCE(r.recipient_name,'-') AS recipient_name, COALESCE(r.institution_name,'-') AS institution_name FROM process_status p LEFT JOIN recipient r ON p.recipient_id = r.recipient_id WHERE p.reg_no = ? ORDER BY p.process_status_id DESC")) {
    $stmt->bind_param("s", $_SESSION['reg_no']);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res) {
      while ($r = $res->fetch_assoc()) {
        $all_statuses[] = $r;
      }
    }
    $stmt->close();
  }

  // Use the latest status (first row) for the summary card
  if (!empty($all_statuses)) {
    $transcript_status = $all_statuses[0];
  }

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

  function request_status_class($status) {
    $s = strtolower(trim((string)($status ?? '')));
    if ($s === '' ) return 'status-empty';
    if (strpos($s,'processed') !== false || strpos($s,'completed') !== false || strpos($s,'approved') !== false || strpos($s,'released') !== false) return 'status-processed';
    return 'status-pending';
  }

  // Prepare completed requests for card view
  $completed_requests = array_filter($all_statuses, function($r){
    $s = strtolower((string)($r['progress_status'] ?? ''));
    return $s === 'completed' || strpos($s, 'completed') !== false || $s === 'approved' || strpos($s,'released') !== false || strpos($s,'processed') !== false;
  });

  ?>
  <!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="./assets/css/student-dashboard.css" />
  <link rel="stylesheet" href="./assets/css/transcript-status.css" />
  <!-- Inline styles removed; styles now live in assets/css/transcript-status.css -->
  <title>Student Dashboard: Transcript Status</title>
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
        
        <?php if (!empty($completed_requests)): ?>
          <h3>Completed Requests</h3>
          <div class="completed-requests">
            <?php foreach ($completed_requests as $cr): ?>
              <article class="request-card <?php echo request_status_class($cr['progress_status']); ?>" data-request-id="<?php echo (int)$cr['process_status_id']; ?>">
                <div class="recipient-card">
                  <div class="recipient-card-header">
                    <h4>Recipient Information</h4>
                  </div>
                  <dl class="recipient-details">
                    <div>
                      <dt>Name</dt>
                      <dd><?php echo htmlspecialchars($cr['recipient_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></dd>
                    </div>
                    <div>
                      <dt>Institution</dt>
                      <dd><?php echo htmlspecialchars($cr['institution_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></dd>
                    </div>
                  </dl>
                </div>
                <div class="card-head">
                  <h4>Completed Request <small class="small">— <?php echo htmlspecialchars(ucfirst($cr['progress_status'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></small></h4>
                  <div class="card-actions">
                    <a class="btn btn-outline" href="print-slip.php?request_id=<?php echo (int)$cr['process_status_id']; ?>&recipient_id=<?php echo (int)($cr['recipient_id'] ?? 0); ?>&reg_no=<?php echo urlencode($cr['reg_no'] ?? ''); ?>" aria-label="View slip for request <?php echo (int)$cr['process_status_id']; ?>">View Slip</a>
                    <a class="btn" href="print-slip.php?request_id=<?php echo (int)$cr['process_status_id']; ?>&recipient_id=<?php echo (int)($cr['recipient_id'] ?? 0); ?>&reg_no=<?php echo urlencode($cr['reg_no'] ?? ''); ?>" aria-label="Download PDF for request <?php echo (int)$cr['process_status_id']; ?>">Download PDF</a>
                  </div>
                </div>
                <div class="meta small">Reg No: <code><?php echo htmlspecialchars($cr['reg_no'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></code></div>
                <div class="note"><?php echo nl2br(htmlspecialchars($cr['progress_note'] ?? 'No note provided', ENT_QUOTES, 'UTF-8')); ?></div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <!-- All requests table removed per user request -->
      
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
<script src="./assets/js/student-dashboard.js"></script>
  <script src="./assets/js/transcript-status.js"></script>
</body>
</html>
