<?php
include 'dbConnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['user'];
    $password = $_POST['pass'];

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("SELECT * FROM staff WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the staff data
        $staff = $result->fetch_assoc();
        // Start the session and store staff information
        session_start();
        $_SESSION['staff_name'] = $staff['staff_name'];
        $_SESSION['username'] = $staff['username'];

        // Login successful
        // echo "Login successful!";
        // You can redirect to a dashboard or another page here
        header("Location: staff-dashboard.php");
    } else {
        // Login failed
        // echo "Invalid username or password.";
        // You can redirect to a dashboard or another page here with an error message
        header("Location: index.php?error=invalid_credentials");

    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>