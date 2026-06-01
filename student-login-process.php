<?php
   // Start the session
    session_start();

  // Your PHP code for processing student login goes here
include 'dbConnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['user']);
    $password = trim($_POST['pass']);

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("SELECT * FROM student_profile WHERE reg_no = ? AND state_origin = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Login successful
       // echo "Login successful!";
        // You can redirect to a dashboard or another page here
        $row = $result->fetch_assoc();
        $_SESSION['student_name'] = $row['student_name'];
        $_SESSION['reg_no'] = $row['reg_no'];

        header("Location: student-dashboard.php");
    } else {
        // Login failed
        //echo "Invalid username or password.";
        // You can redirect to a dashboard or another page here with an error message
        header("Location: student-login.php?error=invalid_credentials");

    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>