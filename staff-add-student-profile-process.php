<?php
include 'auth-staff.php';
include 'dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: staff-update-student-profile.php');
    exit;
}

// --- Sanitize & collect fields ---
$student_name = trim($_POST['student_name'] ?? '');
$reg_no       = trim($_POST['reg_no']       ?? '');
$gender       = trim($_POST['gender']       ?? '');
$phone_no     = trim($_POST['phone_no']     ?? '');
$email        = trim($_POST['email']        ?? '');
$department   = trim($_POST['department']   ?? '');
$programme    = trim($_POST['programme']    ?? '');
$level        = trim($_POST['level']        ?? '');
$state_origin = trim($_POST['state_origin'] ?? '');
$lga          = trim($_POST['lga']          ?? '');
$course_study = trim($_POST['course_study'] ?? '');

// --- Validation ---
$errors = [];

if ($student_name === '') $errors[] = 'Student name is required.';
if ($reg_no === '')       $errors[] = 'Registration number is required.';
if ($gender === '')       $errors[] = 'Gender is required.';
if ($department === '')   $errors[] = 'Department is required.';
if ($programme === '')    $errors[] = 'Programme is required.';
if ($level === '')        $errors[] = 'Level is required.';
if ($course_study === '') $errors[] = 'Course of study is required.';
if ($state_origin === '') $errors[] = 'State of origin is required.';
if ($lga === '')          $errors[] = 'LGA is required.';

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email address.';
}

if (!empty($errors)) {
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => implode(' ', $errors),
    ];
    header('Location: staff-update-student-profile.php');
    exit;
}

// --- Check for duplicate reg_no ---
$check_stmt = $conn->prepare(
    "SELECT student_profile_id FROM student_profile WHERE reg_no = ? LIMIT 1"
);
$check_stmt->bind_param('s', $reg_no);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    $check_stmt->close();
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => "A student with registration number \"$reg_no\" already exists.",
    ];
    header('Location: staff-update-student-profile.php');
    exit;
}
$check_stmt->close();

// --- Insert ---
$stmt = $conn->prepare(
    "INSERT INTO student_profile
        (student_name, reg_no, gender, email, phone_no, department, programme, level, course_study, state_origin, lga)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param(
    'sssssssssss',
    $student_name, $reg_no, $gender, $email, $phone_no,
    $department, $programme, $level, $course_study, $state_origin, $lga
);

if ($stmt->execute()) {
    $_SESSION['flash'] = [
        'type'    => 'success',
        'message' => "Student profile for \"$student_name\" ($reg_no) has been saved successfully.",
    ];
} else {
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => 'Database error: ' . htmlspecialchars($conn->error),
    ];
}

$stmt->close();
$conn->close();

header('Location: staff-update-student-profile.php');
exit;
