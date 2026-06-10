<?php
  include 'auth-staff.php';
  include 'dbConnect.php';

  $staff_name = htmlspecialchars($_SESSION['staff_name'] ?? $_SESSION['username'] ?? 'Staff', ENT_QUOTES, 'UTF-8');
  $search = trim($_GET['search'] ?? '');
  $status = trim($_GET['status'] ?? '');

  $whereClauses = [];
  if ($search !== '') {
    $searchEscaped = $conn->real_escape_string($search);
    $whereClauses[] = "(r.reg_no LIKE '%$searchEscaped%' OR s.student_name LIKE '%$searchEscaped%')";
  }

  if ($status !== '') {
    $statusEscaped = $conn->real_escape_string($status);
    $whereClauses[] = "COALESCE((SELECT progress_status FROM process_status WHERE recipient_id = r.recipient_id ORDER BY process_status_id DESC LIMIT 1), (SELECT progress_status FROM process_status WHERE reg_no = r.reg_no AND recipient_id IS NULL ORDER BY process_status_id DESC LIMIT 1), 'submitted') = '$statusEscaped'";
  }

  $whereSql = '';
  if (!empty($whereClauses)) {
    $whereSql = 'WHERE ' . implode(' AND ', $whereClauses);
  }

  $query = "SELECT r.recipient_id, r.reg_no, COALESCE(s.student_name, 'Unknown') AS student_name, COALESCE(s.course_study, '-') AS course_study, COALESCE(r.institution_name, '-') AS institution_name, COALESCE((SELECT progress_status FROM process_status WHERE recipient_id = r.recipient_id ORDER BY process_status_id DESC LIMIT 1), (SELECT progress_status FROM process_status WHERE reg_no = r.reg_no AND recipient_id IS NULL ORDER BY process_status_id DESC LIMIT 1), 'submitted') AS progress_status FROM recipient r LEFT JOIN student_profile s ON r.reg_no = s.reg_no $whereSql ORDER BY r.recipient_id DESC";
  $result = $conn->query($query);
  $applications = [];
  if ($result) {
    while ($row = $result->fetch_assoc()) {
      $applications[] = $row;
    }
  }
  $success_message = '';
  $error_message = '';
  if (isset($_GET['success'])) {
    $success_message = 'Application updated successfully.';
  }
  if (isset($_GET['error'])) {
    $error_message = htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8');
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Staff Dashboard: View Applications</title>
  <link rel="stylesheet" href="./assets/css/staff-dashboard.css" />
  <link rel="stylesheet" href="./assets/css/staff-view-application.css" />
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
        <h2 id="pageTitle">Transcript Applications</h2>
        <p>Search and review submitted transcript requests before processing.</p>
      </div>

      <article class="staff-panel">
        <form class="staff-form" action="staff-view-application.php" method="get">
          <div class="filter-row">
            <div class="form-group">
              <label for="search">Search</label>
              <input type="search" id="search" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Reg no, or Student Name">
            </div>
            <div class="form-group">
              <label for="status">Status</label>
              <select id="status" name="status">
                <option value=""<?php echo $status === '' ? ' selected' : ''; ?>>All statuses</option>
                <option value="submitted"<?php echo $status === 'submitted' ? ' selected' : ''; ?>>Submitted</option>
                <option value="verified"<?php echo $status === 'verified' ? ' selected' : ''; ?>>Verified</option>
                <option value="processing"<?php echo $status === 'processing' ? ' selected' : ''; ?>>Processing</option>
                <option value="approved"<?php echo $status === 'approved' ? ' selected' : ''; ?>>Approved</option>
                <option value="completed"<?php echo $status === 'completed' ? ' selected' : ''; ?>>Completed</option>
              </select>
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
                <th>S/No.</th>
                <th>Student Name</th>
                <th>Reg No</th>
                <th>Course of Study</th>
                <th>Recipient Institution</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($applications)): ?>
                <tr>
                  <td colspan="7">
                    <div class="empty-state">
                      <ion-icon name="folder-open-outline"></ion-icon>
                      <h3>No Applications Loaded</h3>
                      <p>Connect this page to the application table to show submitted requests.</p>
                    </div>
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($applications as $index => $application): ?>
                  <?php
                    $progressStatus = strtolower($application['progress_status']);
                    $canProcess = !in_array($progressStatus, ['processing', 'approved', 'completed'], true);
                  ?>
                  <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo htmlspecialchars($application['student_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($application['reg_no'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($application['course_study'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($application['institution_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                      <?php if ($canProcess): ?>
                        <a class="staff-button" href="staff-process-transcript.php?recipient_id=<?php echo (int) $application['recipient_id']; ?>&reg_no=<?php echo urlencode($application['reg_no']); ?>">Processing</a>
                      <?php else: ?>
                        <span class="status-label"><?php echo ucfirst($progressStatus); ?></span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
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
      <a href="#"><ion-icon name="logo-whatsapp"></ion-icon></a>
      <a href="#"><ion-icon name="logo-google"></ion-icon></a>
      <a href="#"><ion-icon name="logo-linkedin"></ion-icon></a>
    </p>
  </footer>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <script src="./assets/js/staff-dashboard.js"></script>
  <script src="./assets/js/process-toast.js"></script>
</body>
</html>
