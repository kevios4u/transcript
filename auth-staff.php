<?php

// Start the session
  session_start();

  // Check if the staff is logged in
  if (!isset($_SESSION['staff_name']) && (!isset($_SESSION['username']))) {
    // If not logged in, redirect to the login page
    header('Location: index.php?error=not_logged_in');
    exit();
  }

  ?>