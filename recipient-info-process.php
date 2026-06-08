<?php
  include 'auth-student.php';
  include 'dbConnect.php';

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: recipient-info.php');
    exit;
  }

  $reg_no = $_SESSION['reg_no'];
  $action = $_POST['action'] ?? '';
  $error = '';

  function is_processed_status($status) {
    if ($status === null || trim($status) === '') {
      return false;
    }

    $normalized = strtolower(trim($status));
    return strpos($normalized, 'approved') !== false
        || strpos($normalized, 'completed') !== false
        || strpos($normalized, 'ready') !== false
        || strpos($normalized, 'processed') !== false;
  }

  if ($action === 'submit') {
    $recipient_name = trim($_POST['recipient_name'] ?? '');
    $institution_name = trim($_POST['institution_name'] ?? '');
    $institution_address = trim($_POST['institution_address'] ?? '');

    if ($recipient_name === '' || $institution_name === '' || $institution_address === '') {
      $error = 'All recipient fields are required.';
    } else {
      $stmt = $conn->prepare("INSERT INTO recipient (recipient_name, institution_name, institution_address, reg_no) VALUES (?, ?, ?, ?)");
      $stmt->bind_param('ssss', $recipient_name, $institution_name, $institution_address, $reg_no);
      if (!$stmt->execute()) {
        $error = 'Unable to save recipient information.';
      }
      $stmt->close();
    }
  } elseif ($action === 'drop') {
    $recipient_id = intval($_POST['recipient_id'] ?? 0);
    if ($recipient_id <= 0) {
      $error = 'Invalid recipient submission.';
    } else {
      $stmt = $conn->prepare("SELECT recipient_id, reg_no FROM recipient WHERE recipient_id = ? LIMIT 1");
      $stmt->bind_param('i', $recipient_id);
      $stmt->execute();
      $result = $stmt->get_result();
      $recipient = $result->fetch_assoc();
      $stmt->close();

      if (! $recipient || $recipient['reg_no'] !== $reg_no) {
        $error = 'Submission not found.';
      } else {
        $statusStmt = $conn->prepare("SELECT progress_status FROM process_status WHERE reg_no = ? ORDER BY process_status_id DESC LIMIT 1");
        $statusStmt->bind_param('s', $reg_no);
        $statusStmt->execute();
        $statusResult = $statusStmt->get_result();
        $statusRow = $statusResult->fetch_assoc();
        $statusStmt->close();

        if (is_processed_status($statusRow['progress_status'] ?? null)) {
          $error = 'Cannot drop a submission that has already been processed.';
        } else {
          $deleteStmt = $conn->prepare("DELETE FROM recipient WHERE recipient_id = ?");
          $deleteStmt->bind_param('i', $recipient_id);
          if (!$deleteStmt->execute()) {
            $error = 'Failed to drop the recipient submission.';
          }
          $deleteStmt->close();
        }
      }
    }
  } else {
    $error = 'Invalid action.';
  }

  $conn->close();

  $query = 'recipient-info.php';
  if ($error !== '') {
    $query .= '?error=' . urlencode($error);
  } else {
    $query .= '?success=' . ($action === 'drop' ? 'drop' : 'submit');
  }

  header('Location: ' . $query);
  exit;
?>
