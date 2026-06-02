<!-- prepare student dashboard with menu bar list of recipients information, application status, print slip, application history, and logout button, welcome message with student name, footer with contact information and social media links, and a responsive design for mobile devices. -->
 <?php
  include 'auth-student.php';

  // Get the student's name from the session
  $student_name = $_SESSION['student_name'];
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Dashboard</title>
  <link rel="stylesheet" href="./assets/css/student-dashboard.css" />
</head>
<body>
    <header class="dashboard-header">
      <h1><ion-icon name="person"></ion-icon> Welcome,<span><?php echo $student_name; ?>!</span></h1>
      <nav class="dashboard-nav">
        <a href="#">Recipient Information</a>
        <a href="#">Student Profile</a>
        <a href="#">Transcript Status</a>
        <a href="#">Print Slip</a>
        <a href="#">Logout</a>
      </nav>
      <div class="dashboard-menu"><ion-icon name="menu"></ion-icon></div>
    </header>

    <main class="dashboard-main">
      <!-- Content for each menu item will go here -->
    </main>
  <footer class="dashboard-footer">
    <p>Contact us:07012345678 | Email:info@yahoo.com</p>
    <p>Follow us on social media:
      <a href="#">Facebook</a> |
      <a href="#">Twitter</a> |
      <a href="#">Instagram</a>
    </p>
  </footer>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>