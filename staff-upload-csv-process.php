<?php
include 'auth-staff.php';
include 'dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: staff-update-student-profile.php');
    exit;
}

// --- Validate uploaded file ---
if (!isset($_FILES['student_csv']) || $_FILES['student_csv']['error'] === UPLOAD_ERR_NO_FILE) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Please select a CSV file to upload.'];
    header('Location: staff-update-student-profile.php');
    exit;
}

$file = $_FILES['student_csv'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'File upload error (code ' . $file['error'] . '). Please try again.'];
    header('Location: staff-update-student-profile.php');
    exit;
}

// Check extension and MIME
$ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$mime = mime_content_type($file['tmp_name']);
$allowed_mimes = ['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'];

if ($ext !== 'csv' || !in_array($mime, $allowed_mimes)) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Invalid file type. Please upload a valid .csv file.'];
    header('Location: staff-update-student-profile.php');
    exit;
}

// Max 5 MB
if ($file['size'] > 5 * 1024 * 1024) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'File size exceeds the 5 MB limit.'];
    header('Location: staff-update-student-profile.php');
    exit;
}

// --- Parse CSV ---
$handle = fopen($file['tmp_name'], 'r');
if (!$handle) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Could not read the uploaded file.'];
    header('Location: staff-update-student-profile.php');
    exit;
}

// Read and normalise header row
$header_raw = fgetcsv($handle);
if (!$header_raw) {
    fclose($handle);
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'The CSV file appears to be empty.'];
    header('Location: staff-update-student-profile.php');
    exit;
}

$header = array_map(fn($h) => strtolower(trim($h)), $header_raw);

// Required columns
$required_cols = ['student_name', 'reg_no', 'gender', 'department', 'programme', 'level', 'course_study', 'state_origin', 'lga'];
$missing = array_diff($required_cols, $header);

if (!empty($missing)) {
    fclose($handle);
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => 'Missing required columns: ' . implode(', ', $missing) . '. Please use the CSV template.',
    ];
    header('Location: staff-update-student-profile.php');
    exit;
}

// --- Prepare insert statement ---
$stmt = $conn->prepare(
    "INSERT INTO student_profile
        (student_name, reg_no, gender, email, phone_no, department, programme, level, course_study, state_origin, lga)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

$inserted  = 0;
$skipped   = 0;
$row_errors = 0;
$row_num   = 1; // 1 = header row already consumed

while (($row = fgetcsv($handle)) !== false) {
    $row_num++;

    // Skip completely blank rows
    if (count(array_filter($row, fn($v) => trim($v) !== '')) === 0) {
        continue;
    }

    // Map row to associative array
    if (count($row) < count($header)) {
        // Pad short rows
        $row = array_pad($row, count($header), '');
    }
    $data = array_combine($header, array_map('trim', $row));

    // Validate required fields in this row
    $skip_row = false;
    foreach ($required_cols as $col) {
        if (empty($data[$col])) {
            $row_errors++;
            $skip_row = true;
            break;
        }
    }
    if ($skip_row) continue;

    $student_name = $data['student_name'];
    $reg_no       = $data['reg_no'];
    $gender       = $data['gender'];
    $email        = $data['email']    ?? '';
    $phone_no     = $data['phone_no'] ?? '';
    $department   = $data['department'];
    $programme    = $data['programme'];
    $level        = $data['level'];
    $course_study = $data['course_study'];
    $state_origin = $data['state_origin'];
    $lga          = $data['lga'];

    // Validate email if provided
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email = '';
    }

    // Check duplicate reg_no
    $check = $conn->prepare("SELECT student_profile_id FROM student_profile WHERE reg_no = ? LIMIT 1");
    $check->bind_param('s', $reg_no);
    $check->execute();
    $check->store_result();
    $is_dup = $check->num_rows > 0;
    $check->close();

    if ($is_dup) {
        $skipped++;
        continue;
    }

    $stmt->bind_param(
        'sssssssssss',
        $student_name, $reg_no, $gender, $email, $phone_no,
        $department, $programme, $level, $course_study, $state_origin, $lga
    );

    if ($stmt->execute()) {
        $inserted++;
    } else {
        $row_errors++;
    }
}

fclose($handle);
$stmt->close();
$conn->close();

// --- Build result message ---
$parts = [];
if ($inserted > 0)   $parts[] = "$inserted record(s) imported successfully";
if ($skipped > 0)    $parts[] = "$skipped skipped (duplicate registration numbers)";
if ($row_errors > 0) $parts[] = "$row_errors row(s) had errors and were skipped";

$type = ($inserted > 0) ? 'success' : 'error';
$message = $inserted === 0 && $skipped === 0 && $row_errors === 0
    ? 'The CSV file contained no valid data rows.'
    : implode('; ', $parts) . '.';

$_SESSION['flash'] = ['type' => $type, 'message' => ucfirst($message)];
header('Location: staff-update-student-profile.php');
exit;
