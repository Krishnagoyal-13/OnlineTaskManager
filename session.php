<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlinetaskmanager";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare SQL statement to retrieve user from database
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // User found, verify password
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password_hash'])) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            // Generate session key
            $session_key = generateSessionKey();
            $user_id = $row['user_id'];
            $session_data = json_encode($_SESSION);
            $created_at = date("Y-m-d H:i:s");

            // Insert session key into session key table
            $sql_insert_session = "INSERT INTO session_keys (session_id, user_id, session_data, created_at, updated_at)
                                   VALUES ('$session_key', '$user_id', '$session_data', '$created_at', '$created_at')";
            $conn->query($sql_insert_session);

            // Redirect to dashboard or wherever you want
            header("Location: dashboard.php");
            exit();
        } else {
            // Incorrect password
            echo "Incorrect username or password.";
        }
    } else {
        // User not found
        echo "Incorrect username or password.";
    }
}

// Close connection
$conn->close();

// Function to generate a unique session key
function generateSessionKey() {
    return md5(uniqid(rand(), true));
}
?>
