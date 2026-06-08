<?php
  include 'auth-staff.php';
  include 'dbConnect.php';

  if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['reg_no'])) {
    header('Location: staff-view-application.php');
    exit;
  }

  $reg_no = trim($_POST['reg_no']);
  $recipient_id = intval($_POST['recipient_id'] ?? 0);
  $next_status = trim($_POST['next_status'] ?? 'processing');
  $validStatuses = ['submitted', 'reviewed', 'processing', 'approved', 'completed'];

  if (!in_array($next_status, $validStatuses, true)) {
    $next_status = 'processing';
  }

  if ($recipient_id <= 0) {
    header('Location: staff-view-application.php?error=invalid_request');
    exit;
  }

  $reg_no_safe = $conn->real_escape_string($reg_no);
  $recipient_id_safe = $conn->real_escape_string((string) $recipient_id);
  $progress_note = $conn->real_escape_string("Moved to $next_status from application view.");

  $check = $conn->query("SELECT process_status_id FROM process_status WHERE recipient_id = '$recipient_id_safe' LIMIT 1");

  if ($check && $check->num_rows > 0) {
    $conn->query("UPDATE process_status SET progress_status = '$next_status', progress_note = '$progress_note' WHERE recipient_id = '$recipient_id_safe'");
  } else {
    $conn->query("INSERT INTO process_status (recipient_id, reg_no, progress_status, progress_note) VALUES ('$recipient_id_safe', '$reg_no_safe', '$next_status', '$progress_note')");
  }

  header('Location: staff-view-application.php?success=1');
  exit;
?>
