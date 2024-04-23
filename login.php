<?php
session_start();

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

// Process login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check user credentials against database
    $stmt = $conn->prepare("SELECT user_id, username, password_hash FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password_hash'])) {
            // Password is correct, create session key and store user information
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            
            // Check if a session entry already exists for the user
            $user_id = $row['user_id'];
            $check_sql = "SELECT * FROM sessions WHERE user_id = $user_id";
            $check_result = $conn->query($check_sql);
            
            if ($check_result->num_rows == 0) {
                // Insert session data into the sessions table
                $session_id = session_id() . $user_id;
                $session_data = json_encode($_SESSION);
                $created_at = date("Y-m-d H:i:s"); // Current timestamp

                $insert_sql = "INSERT INTO sessions (session_id, user_id, session_data, created_at) VALUES ('$session_id', $user_id, '$session_data', '$created_at')";
                $conn->query($insert_sql);
            }
            
            // Redirect to dashboard
            header("location: dashboard.php");
            exit;
        } else {
            // Invalid username or password, show alert message and redirect to dashboard
            echo "<script>alert('Invalid username or password.'); window.location.href = 'dashboard.php';</script>";
        }
    } else {
        // Invalid username or password, show alert message and redirect to dashboard
        echo "<script>alert('Invalid username or password.'); window.location.href = 'dashboard.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet">
</head>
<style>
    /* styles.css */
body {
    font-family: Arial, sans-serif;
    background-color: #f2f2f2;
    margin: 0;
    padding: 0;
}

.container {
    width: 300px;
    margin: 100px auto;
    background-color: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

label {
    display: block;
    margin-bottom: 5px;
    color: #333;
}

input[type="text"],
input[type="password"],
input[type="submit"] {
    width: 100%;
    padding: 10px;
    margin: 5px 0 20px;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 5px;
}

input[type="submit"] {
    background-color: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #45a049;
}

.error {
    color: #ff0000;
    text-align: center;
}

</style>
<body>
    <div class="container">
        <h2>User Login</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br><br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>
