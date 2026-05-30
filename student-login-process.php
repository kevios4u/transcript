<?php
  // Your PHP code for processing student login goes here
include 'dbConnect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['user'];
    $password = $_POST['pass'];

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("SELECT * FROM students WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Login successful
        echo "Login successful!";
        // You can redirect to a dashboard or another page here
        // header("Location: student-dashboard.php");
    } else {
        // Login failed
        echo "Invalid username or password.";
        // You can redirect to a dashboard or another page here with an error message
        // header("Location: student-login.php?error=invalid_credentials");

    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>