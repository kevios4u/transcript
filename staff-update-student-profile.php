<?php
  include 'auth-staff.php';

  // --- CSV template download ---
  if (isset($_GET['action']) && $_GET['action'] === 'download_template') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="student_profile_template.csv"');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    echo "student_name,reg_no,gender,email,phone_no,department,programme,level,course_study,state_origin,lga\n";
    echo "John Doe,REG/2024/001,Male,john@example.com,08012345678,Leather Technology,B.Tech,II,Leather Technology,Kaduna,Zaria\n";
    exit;
  }

  $staff_name = htmlspecialchars($_SESSION['staff_name'] ?? $_SESSION['username'] ?? 'Staff', ENT_QUOTES, 'UTF-8');

  // --- Consume flash message ---
  $flash = $_SESSION['flash'] ?? null;
  unset($_SESSION['flash']);

  // --- Fetch all student profiles ---
  include 'dbConnect.php';
  $sq = $conn->query(
    "SELECT student_profile_id, student_name, reg_no, gender, email, phone_no,
            department, programme, level, course_study, state_origin, lga
     FROM student_profile
     ORDER BY student_name ASC"
  );
  $students = $sq ? $sq->fetch_all(MYSQLI_ASSOC) : [];
  $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Staff Dashboard: Update Students</title>
  <link rel="stylesheet" href="./assets/css/staff-dashboard.css" />
  <link rel="stylesheet" href="./assets/css/staff-update-student-profile.css" />
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

    <?php if ($flash): ?>
    <div class="flash-alert flash-<?php echo htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8'); ?>" role="alert" id="flashAlert">
      <ion-icon name="<?php echo $flash['type'] === 'success' ? 'checkmark-circle-outline' : 'alert-circle-outline'; ?>"></ion-icon>
      <div>
        <strong><?php echo $flash['type'] === 'success' ? 'Success' : 'Error'; ?></strong>
        <p><?php echo htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8'); ?></p>
      </div>
      <button class="flash-close" onclick="this.closest('.flash-alert').remove()" aria-label="Close alert">&times;</button>
    </div>
    <?php endif; ?>

    <section class="staff-section" aria-labelledby="pageTitle">
      <div class="overview-heading">
        <h2 id="pageTitle">Update Student Profiles</h2>
        <p>Upload a CSV file for bulk student records or add one student profile manually.</p>
      </div>

      <div class="staff-grid">

        <!-- ── CSV Upload Panel ─────────────────────────────────────── -->
        <article class="staff-panel">
          <div class="panel-heading">
            <ion-icon class="panel-icon" name="cloud-upload-outline"></ion-icon>
            <div>
              <h3>Upload CSV</h3>
              <p>Use this for bulk profile updates from the registry office.</p>
            </div>
          </div>

          <form class="staff-form" id="csvForm" action="staff-upload-csv-process.php" method="post" enctype="multipart/form-data" novalidate>

            <div class="form-group">
              <label>Student CSV File</label>
              <div class="csv-drop-zone" id="csvDropZone" role="button" tabindex="0" aria-label="Click or drag a CSV file here">
                <ion-icon name="document-attach-outline"></ion-icon>
                <p>Drag &amp; drop your CSV here, or <strong>click to browse</strong></p>
                <span>Accepted: .csv &nbsp;|&nbsp; Max size: 5 MB</span>
                <input type="file" id="student_csv" name="student_csv" accept=".csv">
              </div>
              <div class="file-info" id="fileInfo">
                <ion-icon name="document-text-outline"></ion-icon>
                <span id="fileName">No file selected</span>
                <button type="button" class="file-clear" id="fileClear" aria-label="Remove selected file">&times;</button>
              </div>
            </div>

            <div class="csv-preview-wrap" id="csvPreviewWrap">
              <p class="csv-preview-label">Preview (first 5 rows)</p>
              <div class="csv-preview-scroller">
                <table class="csv-preview-table" id="csvPreviewTable">
                  <thead><tr id="csvPreviewHead"></tr></thead>
                  <tbody id="csvPreviewBody"></tbody>
                </table>
              </div>
              <p class="csv-preview-note" id="csvPreviewNote"></p>
            </div>

            <div class="button-row">
              <button class="staff-button" type="submit" id="csvSubmitBtn" disabled>
                <ion-icon name="cloud-upload-outline"></ion-icon>
                Upload Students
              </button>
              <a class="staff-button secondary template-link" href="staff-update-student-profile.php?action=download_template" id="templateDownload">
                <ion-icon name="download-outline"></ion-icon>
                Download Template
              </a>
            </div>
          </form>
        </article>

        <!-- ── Add Student Panel ───────────────────────────────────── -->
        <article class="staff-panel">
          <div class="panel-heading">
            <ion-icon class="panel-icon" name="person-add-outline"></ion-icon>
            <div>
              <h3>Add Student</h3>
              <p>Create a single student profile when CSV upload is not needed.</p>
            </div>
          </div>

          <form class="staff-form" id="addStudentForm" action="staff-add-student-profile-process.php" method="post" novalidate>
            <div class="form-grid">
              <div class="form-group">
                <label for="student_name">Student Name <span style="color:#c0392b">*</span></label>
                <input type="text" id="student_name" name="student_name" placeholder="e.g. Amina Yusuf" required>
              </div>
              <div class="form-group">
                <label for="reg_no">Registration Number <span style="color:#c0392b">*</span></label>
                <input type="text" id="reg_no" name="reg_no" placeholder="e.g. NDLT2025/116" required>
              </div>
              <div class="form-group">
                <label for="gender">Gender <span style="color:#c0392b">*</span></label>
                <select id="gender" name="gender" required>
                  <option value="">Select gender</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                </select>
              </div>
              <div class="form-group">
                <label for="phone_no">Phone Number</label>
                <input type="tel" id="phone_no" name="phone_no" placeholder="e.g. 08012345678">
              </div>
              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="e.g. student@example.com">
              </div>
              <div class="form-group">
                <label for="department">Department <span style="color:#c0392b">*</span></label>
                <input type="text" id="department" name="department" placeholder="e.g. Leather Technology" required>
              </div>
              <div class="form-group">
                <label for="programme">Programme <span style="color:#c0392b">*</span></label>
                <select id="programme" name="programme" required>
                  <option value="">Select programme</option>
                  <option value="Diploma">Diploma</option>
                  <option value="National Diploma">National Diploma</option>
                  <option value="Higher National Diploma">Higher National Diploma</option>
                </select>
              </div>
              <div class="form-group">
                <label for="level">Level <span style="color:#c0392b">*</span></label>
                <select id="level" name="level" required>
                  <option value="">Select level</option>
                  <option value="II">II</option>
                </select>
              </div>
              <div class="form-group">
                <label for="state_origin">State of Origin <span style="color:#c0392b">*</span></label>
                <select id="state_origin" name="state_origin" required>
                  <option value="">Select state</option>
                  <option value="Abia">Abia</option>
                  <option value="Adamawa">Adamawa</option>
                  <option value="Akwa Ibom">Akwa Ibom</option>
                  <option value="Anambra">Anambra</option>
                  <option value="Bauchi">Bauchi</option>
                  <option value="Bayelsa">Bayelsa</option>
                  <option value="Benue">Benue</option>
                  <option value="Borno">Borno</option>
                  <option value="Cross River">Cross River</option>
                  <option value="Delta">Delta</option>
                  <option value="Ebonyi">Ebonyi</option>
                  <option value="Edo">Edo</option>
                  <option value="Ekiti">Ekiti</option>
                  <option value="Enugu">Enugu</option>
                  <option value="FCT">FCT</option>
                  <option value="Gombe">Gombe</option>
                  <option value="Imo">Imo</option>
                  <option value="Jigawa">Jigawa</option>
                  <option value="Kaduna">Kaduna</option>
                  <option value="Kano">Kano</option>
                  <option value="Katsina">Katsina</option>
                  <option value="Kebbi">Kebbi</option>
                  <option value="Kogi">Kogi</option>
                  <option value="Kwara">Kwara</option>
                  <option value="Lagos">Lagos</option>
                  <option value="Nasarawa">Nasarawa</option>
                  <option value="Niger">Niger</option>
                  <option value="Ogun">Ogun</option>
                  <option value="Ondo">Ondo</option>
                  <option value="Osun">Osun</option>
                  <option value="Oyo">Oyo</option>
                  <option value="Plateau">Plateau</option>
                  <option value="Rivers">Rivers</option>
                  <option value="Sokoto">Sokoto</option>
                  <option value="Taraba">Taraba</option>
                  <option value="Yobe">Yobe</option>
                  <option value="Zamfara">Zamfara</option>
                </select>
              </div>
              <div class="form-group">
                <label for="lga">LGA <span style="color:#c0392b">*</span></label>
                <select id="lga" name="lga" disabled required>
                  <option value="">Select state first</option>
                </select>
              </div>
              <div class="form-group full-width">
                <label for="course_study">Course of Study <span style="color:#c0392b">*</span></label>
                <input type="text" id="course_study" name="course_study" placeholder="e.g. Leather Technology" required>
              </div>
            </div>
            <button class="staff-button" type="submit">
              <ion-icon name="save-outline"></ion-icon>
              Save Student
            </button>
          </form>
        </article>

      </div>

      <!-- ── Student Records Table ─────────────────────────────────── -->
      <div class="student-records-section" id="studentRecords">
        <div class="records-header">
          <div>
            <h2 class="records-title">Student Records</h2>
            <p class="records-subtitle">
              <?php $total = count($students); echo $total; ?> student<?php echo $total !== 1 ? 's' : ''; ?> on file
            </p>
          </div>
          <div class="records-controls">
            <div class="search-box">
              <ion-icon name="search-outline"></ion-icon>
              <input type="text" id="studentSearch" placeholder="e.g. name, reg no, department…" autocomplete="off">
            </div>
            <select id="programmeFilter" class="filter-select">
              <option value="">All Programmes</option>
              <option value="Diploma">Diploma</option>
              <option value="National Diploma">National Diploma</option>
              <option value="Higher National Diploma">Higher National Diploma</option>
            </select>
          </div>
        </div>

        <?php if ($total > 0): ?>
        <div class="table-wrap" id="studentTableWrap">
          <table class="staff-table" id="studentTable">
            <thead>
              <tr>
                <th>S/No.</th>
                <th>Student Name</th>
                <th>Reg No</th>
                <th>Department</th>
                <th>Programme</th>
                <th>Level</th>
                <th>Course of Study</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="studentTableBody">
              <?php foreach ($students as $i => $s): ?>
              <?php
                $student_name = $s['student_name'] ?? '';
                $reg_no = $s['reg_no'] ?? '';
                $department = $s['department'] ?? '';
                $programme = $s['programme'] ?? '';
                $level = $s['level'] ?? '';
                $course_study = $s['course_study'] ?? '';
              ?>
              <tr class="student-row"
                  data-search="<?php echo htmlspecialchars(strtolower($student_name . ' ' . $reg_no . ' ' . $department . ' ' . $programme . ' ' . $level . ' ' . $course_study), ENT_QUOTES, 'UTF-8'); ?>"
                  data-programme="<?php echo htmlspecialchars($programme, ENT_QUOTES, 'UTF-8'); ?>">
                <td><?php echo $i + 1; ?></td>
                <td><?php echo htmlspecialchars($student_name, ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($reg_no,       ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($department,   ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($programme,    ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($level,        ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($course_study, ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                  <form class="delete-student-form" action="staff-delete-student-profile-process.php" method="post" data-student-name="<?php echo htmlspecialchars($student_name, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="student_profile_id" value="<?php echo (int) $s['student_profile_id']; ?>">
                    <input type="hidden" name="reg_no" value="<?php echo htmlspecialchars($reg_no, ENT_QUOTES, 'UTF-8'); ?>">
                    <button class="delete-record-btn" type="submit" title="Delete student record">
                      <ion-icon name="trash-outline"></ion-icon>
                      Delete
                    </button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <p class="records-count-note" id="recordsCountNote"></p>
        <?php else: ?>
        <div class="empty-state">
          <ion-icon name="people-outline"></ion-icon>
          <h3>No Student Records Yet</h3>
          <p>Upload a CSV file or add a student manually above to get started.</p>
        </div>
        <?php endif; ?>
      </div>

    </section>
  </main>

  <footer class="dashboard-footer">
    <p>Contact us: <ion-icon name="call-outline"></ion-icon> 07012345678 &nbsp; <ion-icon name="mail-outline"></ion-icon> info@yahoo.com</p>
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
  <script src="./assets/js/staff-update-student-profile.js"></script>
</body>
</html>
