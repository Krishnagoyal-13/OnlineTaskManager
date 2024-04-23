<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page or display an error message
    header("location: dashboard.php");
    exit; // Stop further execution
}

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

// Process update/delete task form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['update'])) {
        $task_id = $_POST['task_id'];
        $task_name = $_POST['task_name'];
        $description = $_POST['description'];
        $priority = $_POST['priority'];
        $due_date = $_POST['due_date'];

        // Prepare and execute SQL query to update task
        $sql = "UPDATE tasks SET task_name=?, description=?, priority=?, due_date=? WHERE task_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $task_name, $description, $priority, $due_date, $task_id);
        if ($stmt->execute()) {
            // Task updated successfully
            echo "<script>alert('Task updated successfully');</script>";
            echo "<script>window.location.href = 'dashboard.php';</script>"; // Redirect to dashboard
        } else {
            // Error occurred while updating task
            echo "<script>alert('Error updating task');</script>";
        }
        $stmt->close();
    }
    elseif(isset($_POST['delete'])) {
        $task_id = $_POST['task_id'];

        // Prepare and execute SQL query to delete task
        $sql = "DELETE FROM tasks WHERE task_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $task_id);
        if ($stmt->execute()) {
            // Task deleted successfully
            echo "<script>alert('Task deleted successfully');</script>";
            echo "<script>window.location.href = 'dashboard.php';</script>"; // Redirect to dashboard
        } else {
            // Error occurred while deleting task
            echo "<script>alert('Error deleting task');</script>";
        }
        $stmt->close();
    }
}

// Fetch all tasks data after update
$sql = "SELECT * FROM tasks WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$tasks = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Task</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Additional styles for Update Task page */
        .container {
            display: flex;
            align-items: flex-start; /* Align items to the top */
        }

        aside {
            width: 20%; /* Match width of sidebar */
            padding: 20px; /* Match padding of sidebar */
        }

        main {
            flex: 1; /* Take up remaining space */
            padding: 20px;
            background-color: #fff; /* Match background color of task list */
            border: 1px solid #ddd; /* Match border style of task list */
            border-radius: 5px; /* Match border radius of task list */
            margin-left: 20px; /* Add margin to separate from sidebar */
        }

        form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        input[type="text"],
        textarea,
        select,
        input[type="date"],
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            display: block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <!--<h1>Task List</h1>-->
        <ul>
            <?php foreach ($tasks as $task) : ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                    <input type="text" name="task_name" value="<?php echo $task['task_name']; ?>" required>
                    <textarea name="description" required><?php echo $task['description']; ?></textarea>
                    <select name="priority" required>
                        <option value="High" <?php echo ($task['priority'] == 'High') ? 'selected' : ''; ?>>High</option>
                        <option value="Medium" <?php echo ($task['priority'] == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                        <option value="Low" <?php echo ($task['priority'] == 'Low') ? 'selected' : ''; ?>>Low</option>
                    </select>
                    <input type="date" name="due_date" value="<?php echo $task['due_date']; ?>" required>
                    <button type="submit" name="update" onclick="return confirm('Are you sure you want to update this task?')">Update</button>
                    <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this task?')">Delete</button>
                </form>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
