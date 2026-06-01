<?php

// Start the session
  session_start();

  // Check if the student is logged in
  if (!isset($_SESSION['student_name']) && (!isset($_SESSION['reg_no']))) {
    // If not logged in, redirect to the login page
    header('Location: student-login.php?error=not_logged_in');
    exit();
  }

  ?>