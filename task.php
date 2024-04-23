<?php
session_start();

// Redirect to dashboard if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("location: dashboard.php");
    exit;
}

// Process form submission to add new task
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    // Prepare and bind parameters for SQL statement
    $stmt = $conn->prepare("INSERT INTO tasks (user_id, task_name, description, priority, due_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $task_name, $description, $priority, $due_date);

    // Set parameters from form data
    $user_id = $_SESSION['user_id'];
    $task_name = $_POST['task_name'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];

    // Execute statement
    $stmt->execute();

    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Redirect to task.php page with success message
    $_SESSION['success_message'] = "New task added successfully";
    header("location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Scope the styles specific to task.php within a unique class or ID */
        .task-page {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .task-container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .task-container h1,
        .task-container h2 {
            color: #333;
            text-align: center;
        }

        .task-form {
            margin-bottom: 20px;
        }

        .task-form h2 {
            margin-top: 0;
        }

        .task-form input[type="text"],
        .task-form textarea,
        .task-form select,
        .task-form input[type="date"],
        .task-form button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            display: block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .task-form button {
            background-color: #4caf50;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        .task-form button:hover {
            background-color: #45a049;
        }

        .task-list {
            border-top: 1px solid #ccc;
            padding-top: 20px;
        }

        .task {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .task h3 {
            margin-top: 0;
            color: #333;
        }

        .task p {
            color: #666;
        }

        .task span {
            font-weight: bold;
        }
    </style>
</head>
<body class="task-page">
    <div class="task-container">
        <div class="task-form">
            <h2>Add New Task</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <input type="text" name="task_name" placeholder="Task Name" required>
                <textarea name="description" placeholder="Description" required></textarea>
                <select name="priority" required>
                    <option value="" disabled selected>Select Priority</option>
                    <option value="High">High</option>
                    <option value="Medium">Medium</option>
                    <option value="Low">Low</option>
                </select>
                <input type="date" name="due_date" min="<?php echo date('Y-m-d'); ?>" required>
                <button type="submit">Add Task</button>
            </form>
        </div>
    </div>
</body>
<script>
    $(document).ready(function() {
        // Event listener for updating tasks
        $(".update-task-form").submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var formData = form.serialize(); // Serialize form data
            var url = form.attr("action");

            // Send AJAX request to update_task.php
            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                success: function(response) {
                    // Display success message
                    alert(response);
                },
                error: function(xhr, status, error) {
                    // Display error message
                    alert("Error updating task: " + error);
                }
            });
        });
    });
</script>
</html>
