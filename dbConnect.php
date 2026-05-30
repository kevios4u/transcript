<?php
$conn = new mysqli("localhost", "root", "", "transcript");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>