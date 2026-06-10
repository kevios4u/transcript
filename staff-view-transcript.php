<?php
  include 'auth-staff.php';
  include 'dbConnect.php';

  $staff_name = htmlspecialchars($_SESSION['staff_name'] ?? $_SESSION['username'] ?? 'Staff', ENT_QUOTES, 'UTF-8');

  $success_message = '';
  $error_message = '';

  // Handle release/archive actions
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $recipient_id = intval($_POST['recipient_id'] ?? 0);
    $reg_no = trim($_POST['reg_no'] ?? '');
    if ($action === 'release' || $action === 'archive') {
      $status = $action === 'release' ? 'released' : 'archived';
      $note = $conn->real_escape_string(($action === 'release' ? 'Marked released' : 'Archived by staff'));
      $reg_safe = $conn->real_escape_string($reg_no);
      if ($recipient_id > 0) {
        $rid = $conn->real_escape_string((string) $recipient_id);
        $conn->query("INSERT INTO process_status (recipient_id, reg_no, progress_status, progress_note) VALUES ('$rid', '$reg_safe', '$status', '$note')");
      } else {
        $conn->query("INSERT INTO process_status (recipient_id, reg_no, progress_status, progress_note) VALUES (NULL, '$reg_safe', '$status', '$note')");
      }
      if ($conn->error) {
        $error_message = 'Database error: ' . $conn->error;
      } else {
        $success_message = ucfirst($status) . ' successfully.';
      }
    }
  }
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
          <div class="table-wrap">
            <?php
              // show toast if messages
              if (!empty($success_message) || !empty($error_message)) {
                echo '<div id="processToast" class="notification-toast ' . (!empty($error_message) ? 'error' : 'success') . '"><p>' . ($error_message ?: $success_message) . '</p><button type="button" class="notification-close" onclick="document.getElementById(\'processToast\')?.remove();">&times;</button></div>';
              }

              // Fetch latest process_status entries where status indicates completion or release
              $completed = [];
            if (isset($conn)) {
              $statuses = "'approved','completed','released','archived'";
              $sql = "SELECT p.process_status_id, p.recipient_id, p.reg_no, p.progress_status, p.progress_note, COALESCE(s.student_name, 'Unknown') AS student_name FROM process_status p LEFT JOIN recipient r ON p.recipient_id = r.recipient_id LEFT JOIN student_profile s ON COALESCE(r.reg_no, p.reg_no) = s.reg_no WHERE p.process_status_id = (SELECT MAX(p2.process_status_id) FROM process_status p2 WHERE (p2.recipient_id = p.recipient_id OR (p2.recipient_id IS NULL AND p2.reg_no = p.reg_no))) AND p.progress_status IN ($statuses) ORDER BY p.process_status_id DESC";
              $res = $conn->query($sql);
              if ($res) {
                while ($row = $res->fetch_assoc()) $completed[] = $row;
              }
            }
            ?>

            <table class="staff-table">
              <thead>
                <tr>
                  <th class="id-column">Transcript ID</th>
                  <th>Student</th>
                  <th>Reg No</th>
                  <th>Note</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($completed)): ?>
                  <tr>
                    <td colspan="5">
                      <div class="empty-state">
                        <ion-icon name="document-attach-outline"></ion-icon>
                        <h3>No Processed Transcripts</h3>
                        <p>Completed transcript records will appear here after processing is connected.</p>
                      </div>
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($completed as $row): ?>
                    <tr>
                      <td class="id-column"><?php echo (int) $row['process_status_id']; ?></td>
                      <td><?php echo htmlspecialchars($row['student_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                      <td><?php echo htmlspecialchars($row['reg_no'], ENT_QUOTES, 'UTF-8'); ?></td>
                      <td class="note-column"><?php echo htmlspecialchars($row['progress_note'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                      <td><?php echo htmlspecialchars(ucfirst($row['progress_status']), ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </form>
      </article>
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
  <script src="./assets/js/staff-view-transcript.js"></script>
</body>
</html>
