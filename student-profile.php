<?php

  include 'dbConnect.php';
  include 'auth-student.php';

  // Get the student's name from the session
  $student_name = htmlspecialchars($_SESSION['student_name'], ENT_QUOTES, 'UTF-8');
  $reg_no = htmlspecialchars($_SESSION['reg_no'], ENT_QUOTES, 'UTF-8');

  $profile = [];
  $stmt = $conn->prepare("SELECT student_profile_id, student_name, reg_no, gender, email, phone_no, department, programme, level, course_study, state_origin, lga FROM student_profile WHERE reg_no = ? LIMIT 1");
  $stmt->bind_param("s", $_SESSION['reg_no']);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $profile = $result->fetch_assoc();
  }

  $stmt->close();
  $conn->close();

  function profile_value($profile, $field) {
    return htmlspecialchars($profile[$field] ?? '', ENT_QUOTES, 'UTF-8');
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Dashboard: Student Profile</title>
  <link rel="stylesheet" href="./assets/css/student-dashboard.css" />
  <link rel="stylesheet" href="./assets/css/student-profile.css" />
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

    <main class="student-main">
      <div class="dashboard-welcome">
        <h1>Welcome, <span><?php echo $student_name; ?></span>!</h1>
      </div>
      <section class="student-overview" aria-labelledby="overviewTitle">
        <div class="overview-heading">
          <h2 id="overviewTitle">Student Profile</h2>
          <p>View your personal information.</p>
        </div>

        <?php if (empty($profile)) : ?>
          <p class="profile-empty">No student profile record was found.</p>
        <?php else : ?>
          <form class="student-profile-form" action="#" method="post">
            <input type="hidden" name="student_profile_id" value="<?php echo profile_value($profile, 'student_profile_id'); ?>">

            <div class="form-grid">
              <div class="form-group">
                <label for="student_name">Student Name</label>
                <input type="text" id="student_name" name="student_name" value="<?php echo profile_value($profile, 'student_name'); ?>" readonly>
              </div>

              <div class="form-group">
                <label for="reg_no">Registration Number</label>
                <input type="text" id="reg_no" name="reg_no" value="<?php echo profile_value($profile, 'reg_no'); ?>" readonly>
              </div>

              <div class="form-group">
                <label for="gender">Gender</label>
                <input type="text" id="gender" name="gender" value="<?php echo profile_value($profile, 'gender'); ?>" readonly>
              </div>

              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo profile_value($profile, 'email'); ?>" readonly>
              </div>

              <div class="form-group">
                <label for="phone_no">Phone Number</label>
                <input type="text" id="phone_no" name="phone_no" value="<?php echo profile_value($profile, 'phone_no'); ?>" readonly>
              </div>

              <div class="form-group">
                <label for="department">Department</label>
                <input type="text" id="department" name="department" value="<?php echo profile_value($profile, 'department'); ?>" readonly>
              </div>

              <div class="form-group">
                <label for="programme">Programme</label>
                <input type="text" id="programme" name="programme" value="<?php echo profile_value($profile, 'programme'); ?>" readonly>
              </div>

              <div class="form-group">
                <label for="level">Level</label>
                <input type="text" id="level" name="level" value="<?php echo profile_value($profile, 'level'); ?>" readonly>
              </div>

              <div class="form-group">
                <label for="course_study">Course of Study</label>
                <input type="text" id="course_study" name="course_study" value="<?php echo profile_value($profile, 'course_study'); ?>" readonly>
              </div>

              <div class="form-group">
                <label for="state_origin">State of Origin</label>
                <input type="text" id="state_origin" name="state_origin" value="<?php echo profile_value($profile, 'state_origin'); ?>" readonly>
              </div>

              <div class="form-group">
                <label for="lga">LGA</label>
                <input type="text" id="lga" name="lga" value="<?php echo profile_value($profile, 'lga'); ?>" readonly>
              </div>
            </div>
          </form>
        <?php endif; ?>

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
  <script src="./assets/js/student-profile.js"></script>
</body>
</html>
