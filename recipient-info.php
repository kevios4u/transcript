<?php
  include 'auth-student.php';
  include 'dbConnect.php';

  // Get the student's name from the session
  $student_name = htmlspecialchars($_SESSION['student_name'], ENT_QUOTES, 'UTF-8');
  $reg_no = $_SESSION['reg_no'];
  $success_message = '';
  $error_message = '';

  if (isset($_GET['success'])) {
    $success_message = $_GET['success'] === 'drop' ? 'Recipient entry dropped successfully.' : 'Recipient information submitted successfully.';
  }
  if (isset($_GET['error'])) {
    $error_message = htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8');
  }

  $stmt = $conn->prepare("SELECT recipient_id, recipient_name, institution_name, institution_address, reg_no, (SELECT progress_status FROM process_status WHERE reg_no = recipient.reg_no ORDER BY process_status_id DESC LIMIT 1) AS progress_status FROM recipient WHERE reg_no = ? ORDER BY recipient_id DESC");
  $stmt->bind_param('s', $reg_no);
  $stmt->execute();
  $result = $stmt->get_result();
  $submissions = [];

  while ($row = $result->fetch_assoc()) {
    $submissions[] = $row;
  }

  $stmt->close();
  $conn->close();

  function is_drop_allowed($status) {
    if ($status === null || trim($status) === '') {
      return true;
    }

    $normalized = strtolower(trim($status));
    return strpos($normalized, 'approved') === false
        && strpos($normalized, 'completed') === false
        && strpos($normalized, 'ready') === false
        && strpos($normalized, 'processed') === false;
  }

  function display_status($status) {
    return htmlspecialchars($status ?? 'Submitted', ENT_QUOTES, 'UTF-8');
  }
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

    <main class="recipient-main">
      <div class="dashboard-welcome">
        <h1>Welcome, <span><?php echo $student_name; ?></span>!</h1>
      </div>
      <?php if ($success_message || $error_message): ?>
        <div id="recipientToast" class="notification-toast <?php echo $error_message ? 'error' : 'success'; ?>">
          <p><?php echo $error_message ?: $success_message; ?></p>
          <button type="button" class="notification-close" aria-label="Dismiss notification">&times;</button>
        </div>
      <?php endif; ?>
      <section class="recipient-overview" aria-labelledby="overviewTitle">
        <div class="overview-heading">
          <h2 id="overviewTitle">Recipient Information</h2>
          <p>Fill in the details of the institution, which you want to send your transcript.</p>
        </div>

        <form class="recipient-form" action="recipient-info-process.php" method="post">
          <input type="hidden" name="action" value="submit">

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

        <section class="recipient-list">
          <h3>Submitted Recipient Applications</h3>
          <?php if (empty($submissions)): ?>
            <p class="empty-text">You have not submitted any recipient information yet.</p>
          <?php else: ?>
            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>S/No.</th>
                    <th>Recipient Name</th>
                    <th>Institution</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($submissions as $index => $submission): ?>
                    <?php $dropAllowed = is_drop_allowed($submission['progress_status']); ?>
                    <tr>
                      <td><?php echo $index + 1; ?></td>
                      <td><?php echo htmlspecialchars($submission['recipient_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                      <td><?php echo htmlspecialchars($submission['institution_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                      <td><?php echo htmlspecialchars($submission['institution_address'], ENT_QUOTES, 'UTF-8'); ?></td>
                      <td><?php echo display_status($submission['progress_status']); ?></td>
                      <td>
                        <?php if ($dropAllowed): ?>
                          <form action="recipient-info-process.php" method="post" class="inline-form">
                            <input type="hidden" name="action" value="drop">
                            <input type="hidden" name="recipient_id" value="<?php echo (int) $submission['recipient_id']; ?>">
                            <button type="submit" class="form-drop">Drop</button>
                          </form>
                        <?php else: ?>
                          <span class="status-tag">Locked</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </section>
      </section>
    </main>

    <div class="confirm-modal-overlay" id="dropConfirmOverlay" aria-hidden="true">
      <div class="confirm-modal" role="dialog" aria-modal="true" aria-labelledby="confirmTitle" aria-describedby="confirmMessage">
        <div class="confirm-modal-icon">
          <ion-icon name="warning-outline"></ion-icon>
        </div>
        <div class="confirm-modal-content">
          <h2 id="confirmTitle">Drop Recipient Request?</h2>
          <p id="confirmMessage">This will remove the recipient submission permanently. You cannot undo this action.</p>
        </div>
        <div class="confirm-modal-actions">
          <button type="button" class="confirm-button cancel-button" id="confirmCancel">Cancel</button>
          <button type="button" class="confirm-button confirm-button--danger" id="confirmDrop">Yes, Drop</button>
        </div>
      </div>
    </div>

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
  <script src="./assets/js/recipient-info.js"></script>
</body>
</html>
