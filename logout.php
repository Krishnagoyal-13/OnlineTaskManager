<?php
session_start();

// Destroy the session
session_destroy();

session_regenerate_id(true);

// Redirect to the initial dashboard page
header("location: dashboard.php");

// Establish connection to MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlinetaskmanager";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update the 'updated_at' time in the 'sessions' table
if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $updated_at = date("Y-m-d H:i:s"); // Current timestamp

    $update_sql = "UPDATE sessions SET updated_at='$updated_at' WHERE user_id=$user_id";
    $conn->query($update_sql);
}
?>
