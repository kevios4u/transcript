<?php
  include 'auth-staff.php';
  include 'dbConnect.php';

  $staff_name = htmlspecialchars($_SESSION['staff_name'] ?? $_SESSION['username'] ?? 'Staff', ENT_QUOTES, 'UTF-8');

  $selected_recipient_id = intval($_GET['recipient_id'] ?? 0);
  $selected_reg_no = trim($_GET['reg_no'] ?? '');
  $success_message = '';
  $error_message = '';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reg_no = trim($_POST['reg_no'] ?? '');
    $recipient_id = intval($_POST['recipient_id'] ?? 0);
    $next_status = trim($_POST['process_status'] ?? 'processing');
    $process_note = trim($_POST['process_note'] ?? '');
    $validStatuses = ['submitted', 'verified', 'processing', 'approved', 'completed'];

    if ($reg_no === '') {
      $error_message = 'Registration number is required.';
    } else {
      if (!in_array($next_status, $validStatuses, true)) $next_status = 'processing';
      $reg_no_safe = $conn->real_escape_string($reg_no);
      $process_note_safe = $conn->real_escape_string($process_note ?: "Updated to $next_status");
      $recipient_safe = $recipient_id > 0 ? $conn->real_escape_string((string) $recipient_id) : 'NULL';

      if ($recipient_id > 0) {
        $conn->query("INSERT INTO process_status (recipient_id, reg_no, progress_status, progress_note) VALUES ('$recipient_safe', '$reg_no_safe', '$next_status', '$process_note_safe')");
      } else {
        $conn->query("INSERT INTO process_status (recipient_id, reg_no, progress_status, progress_note) VALUES (NULL, '$reg_no_safe', '$next_status', '$process_note_safe')");
      }

      if ($conn->error) {
        $error_message = 'Database error: ' . $conn->error;
      } else {
        $success_message = 'Request updated to "' . htmlspecialchars($next_status, ENT_QUOTES, 'UTF-8') . '".';
      }
    }
  }
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
          <?php if ($success_message || $error_message): ?>
            <div class="notification-toast <?php echo $error_message ? 'error' : 'success'; ?>" id="processToast">
              <p><?php echo $error_message ?: $success_message; ?></p>
              <button type="button" class="notification-close" aria-label="Dismiss notification" onclick="document.getElementById('processToast')?.remove();">&times;</button>
            </div>
          <?php endif; ?>

          <form class="staff-form" action="staff-process-transcript.php" method="post" id="processForm">
            <input type="hidden" id="recipient_id" name="recipient_id" value="<?php echo $selected_recipient_id; ?>">
            <div class="form-grid">
              <div class="form-group">
                <label for="reg_no">Registration Number</label>
                <input type="text" id="reg_no" name="reg_no" value="<?php echo htmlspecialchars($selected_reg_no, ENT_QUOTES, 'UTF-8'); ?>" required>
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
                <textarea id="process_note" name="process_note" placeholder="Add a note that explains the selected status update"></textarea>
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
          <?php
            // load pending queue: entries with submitted, verified, or processing status
            $pending = [];
            if (isset($conn)) {
              $q = "SELECT r.recipient_id, r.reg_no, COALESCE(s.student_name, 'Unknown') AS student_name, COALESCE(r.institution_name,'-') AS institution_name, COALESCE((SELECT progress_status FROM process_status WHERE recipient_id = r.recipient_id ORDER BY process_status_id DESC LIMIT 1),(SELECT progress_status FROM process_status WHERE reg_no = r.reg_no AND recipient_id IS NULL ORDER BY process_status_id DESC LIMIT 1),'submitted') AS progress_status FROM recipient r LEFT JOIN student_profile s ON r.reg_no = s.reg_no ORDER BY r.recipient_id DESC";
              $res = $conn->query($q);
              if ($res) {
                while ($row = $res->fetch_assoc()) {
                  $status = strtolower($row['progress_status'] ?? 'submitted');
                  if (in_array($status, ['submitted', 'verified', 'processing'], true)) {
                    $pending[] = $row;
                  }
                }
              }
            }
          ?>
          <?php if (empty($pending)): ?>
            <div class="empty-state">
              <ion-icon name="time-outline"></ion-icon>
              <h3>No Pending Requests</h3>
              <p>There are no submitted requests awaiting processing.</p>
            </div>
          <?php else: ?>
            <div class="table-wrap">
              <table class="staff-table">
                <thead>
                  <tr>
                    <th>S/No.</th>
                    <th>Student</th>
                    <th>Reg No</th>
                    <th>Institution</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($pending as $i => $row): ?>
                    <tr>
                      <td><?php echo $i + 1; ?></td>
                      <td><?php echo htmlspecialchars($row['student_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                      <td><?php echo htmlspecialchars($row['reg_no'], ENT_QUOTES, 'UTF-8'); ?></td>
                      <td><?php echo htmlspecialchars($row['institution_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                      <td>
                        <button type="button" class="staff-button select-application" data-recipient="<?php echo (int) $row['recipient_id']; ?>" data-reg="<?php echo htmlspecialchars($row['reg_no'], ENT_QUOTES, 'UTF-8'); ?>">Select</button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
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
      <a href="#"><ion-icon name="logo-whatsapp"></ion-icon></a>
      <a href="#"><ion-icon name="logo-google"></ion-icon></a>
      <a href="#"><ion-icon name="logo-linkedin"></ion-icon></a>
    </p>
  </footer>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <script src="./assets/js/staff-dashboard.js"></script>
  <script src="./assets/js/staff-process-transcript.js"></script>
</body>
</html>
