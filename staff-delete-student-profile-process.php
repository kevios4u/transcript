<?php
include 'auth-staff.php';
include 'dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: staff-update-student-profile.php');
    exit;
}

$student_profile_id = (int) ($_POST['student_profile_id'] ?? 0);
$posted_reg_no = trim($_POST['reg_no'] ?? '');

if ($student_profile_id <= 0 || $posted_reg_no === '') {
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => 'Invalid student record selected for deletion.',
    ];
    header('Location: staff-update-student-profile.php');
    exit;
}

$_SESSION['reg_no'] = $posted_reg_no;
$session_reg_no = trim($_SESSION['reg_no'] ?? '');

$check_stmt = $conn->prepare(
    "SELECT student_profile_id, student_name, reg_no FROM student_profile WHERE reg_no = ? LIMIT 1"
);
$check_stmt->bind_param('s', $session_reg_no);
$check_stmt->execute();
$result = $check_stmt->get_result();
$student = $result ? $result->fetch_assoc() : null;
$check_stmt->close();

if (!$student || (int) $student['student_profile_id'] !== $student_profile_id) {
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => 'The selected student record could not be found.',
    ];
    $conn->close();
    header('Location: staff-update-student-profile.php');
    exit;
}

$reg_no = $session_reg_no;
$related_tables = [];
$tables_result = $conn->query(
    "SELECT DISTINCT TABLE_NAME
     FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
       AND COLUMN_NAME = 'reg_no'
       AND TABLE_NAME <> 'student_profile'"
);

if (!$tables_result) {
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => 'Could not verify whether this registration number exists in other tables.',
    ];
    $conn->close();
    header('Location: staff-update-student-profile.php');
    exit;
}

while ($table = $tables_result->fetch_assoc()) {
    $table_name = $table['TABLE_NAME'];
    $safe_table = '`' . str_replace('`', '``', $table_name) . '`';
    $reference_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM $safe_table WHERE reg_no = ?");

    if (!$reference_stmt) {
        $_SESSION['flash'] = [
            'type'    => 'error',
            'message' => 'Could not verify registration number references in table: ' . $table_name . '.',
        ];
        $conn->close();
        header('Location: staff-update-student-profile.php');
        exit;
    }

    $reference_stmt->bind_param('s', $reg_no);
    $reference_stmt->execute();
    $reference_result = $reference_stmt->get_result();
    $reference_count = $reference_result ? (int) ($reference_result->fetch_assoc()['total'] ?? 0) : 0;
    $reference_stmt->close();

    if ($reference_count > 0) {
        $related_tables[] = $table_name;
    }
}

if (!empty($related_tables)) {
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => 'This student record cannot be deleted because registration number "' . $reg_no . '" exists in other table(s): ' . implode(', ', $related_tables) . '.',
    ];
    $conn->close();
    header('Location: staff-update-student-profile.php');
    exit;
}

$stmt = $conn->prepare("DELETE FROM student_profile WHERE student_profile_id = ? AND reg_no = ? LIMIT 1");
$stmt->bind_param('is', $student_profile_id, $reg_no);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $_SESSION['flash'] = [
        'type'    => 'success',
        'message' => 'Student record for "' . $student['student_name'] . '" (' . $student['reg_no'] . ') has been deleted.',
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
