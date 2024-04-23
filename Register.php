<?php
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

// Process registration form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize user inputs to prevent SQL injection
    $firstname = $conn->real_escape_string($_POST['firstname']);
    $lastname = $conn->real_escape_string($_POST['lastname']);
    $email = $conn->real_escape_string($_POST['email']);
    
    // Check if username and email already exist
    $check_sql = "SELECT * FROM users WHERE username = '$firstname$lastname' OR email = '$email'";
    $result = $conn->query($check_sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['username'] == $firstname.$lastname) {
            echo "<script>alert('User already exists with the username: $firstname$lastname');</script>";
        }
        if ($row['email'] == $email) {
            echo "<script>alert('User already exists with the email: $email');</script>";
        }
        echo "<script>setTimeout(function() { window.location.href = 'Dashboard.php'; }, 1000);</script>";
    } else {
        // Generate a random password
        $password = generatePassword();
        $username = $firstname . $lastname;
        $role = "user";

        // Hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insert user data into the database
        $sql = "INSERT INTO users (username, email, password_hash, role)
                VALUES ('$username', '$email', '$password_hash', 'user')";

        if ($conn->query($sql) === TRUE) {
            // Registration successful, show password in alert
            echo "<script>alert('Registration successful! Your username is: $username & password is: $password');</script>";
            // Set cookie for remembering email
            setcookie("remember_email", $email, time() + (86400 * 30), "/"); // 30 days
            // Redirect to login page
            echo "<script>setTimeout(function() { window.location.href = 'Dashboard.php'; }, 1000);</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Generate a random password
function generatePassword() {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $password = substr(str_shuffle($chars), 0, 8);
    return $password;
}

// Close database connection
$conn->close();
?>
