<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--<title>View Tasks</title>-->
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Add CSS styles for task dates */
        .task-date {
            margin-bottom: 20px;
        }

        .task-date h2 {
            background-color: #f2f2f2;
            padding: 10px;
            margin: 0;
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

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            padding: 5px 10px;
            margin: 0 5px;
            background-color: #f2f2f2;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }

        .pagination a:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <!--<h1>View Tasks</h1>-->
        <div class="task-list" id="task-list">
            <!-- PHP code will be loaded here -->
            <?php
            session_start();
            
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

            // Pagination logic
            $limit = 10; // Number of tasks per page
            $page = isset($_GET['page']) ? $_GET['page'] : 1; // Get current page number
            $offset = ($page - 1) * $limit; // Calculate offset

            // Prepare and execute SQL query to fetch tasks grouped by date
            $user_id = $_SESSION['user_id']; // Assuming you have user authentication and session management
            $sql = "SELECT DISTINCT DATE_FORMAT(due_date, '%Y-%m-%d') AS task_date FROM tasks WHERE user_id = $user_id ORDER BY due_date LIMIT $offset, $limit";
            $result = $conn->query($sql);

            if ($result) {
                // Check if there are any rows returned
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while($row = $result->fetch_assoc()) {
                        $task_date = $row['task_date'];
                        // Fetch tasks for the current date
                        $task_sql = "SELECT * FROM tasks WHERE user_id = $user_id AND DATE_FORMAT(due_date, '%Y-%m-%d') = '$task_date' ORDER BY due_date";
                        $task_result = $conn->query($task_sql);
                        if ($task_result->num_rows > 0) {
                            echo "<div class='task-date'>";
                            echo "<h2>" . $task_date . "</h2>";
                            // Output tasks for the current date
                            while($task_row = $task_result->fetch_assoc()) {
                                echo "<div class='task'>";
                                echo "<h3>" . $task_row["task_name"] . "</h3>";
                                echo "<p><strong>Description:</strong> " . $task_row["description"] . "</p>";
                                echo "<p><strong>Priority:</strong> " . $task_row["priority"] . "</p>";
                                //echo "<p><strong>Due Date:</strong> " . $task_row["due_date"] . "</p>";
                                echo "</div>";
                            }
                            echo "</div>";
                        }
                    }

                    // Pagination links
                    $sql = "SELECT COUNT(DISTINCT DATE_FORMAT(due_date, '%Y-%m-%d')) AS total FROM tasks WHERE user_id = $user_id";
                    $result = $conn->query($sql);
                    $row = $result->fetch_assoc();
                    $total_pages = ceil($row["total"] / $limit); // Calculate total pages
                    echo "<div class='pagination'>";
                    if ($page > 1) {
                        echo "<a href='viewtask.php?page=" . ($page - 1) . "'>&laquo; Previous</a>";
                    }
                    for ($i = 1; $i <= $total_pages; $i++) {
                        echo "<a href='viewtask.php?page=" . $i . "'>" . $i . "</a>";
                    }
                    if ($page < $total_pages) {
                        echo "<a href='viewtask.php?page=" . ($page + 1) . "'>Next &raquo;</a>";
                    }
                    echo "</div>";
                } else {
                    echo "<p>No tasks found.</p>";
                }
            } else {
                echo "Error: " . $conn->error;
            }

            // Close connection
            $conn->close();
            ?>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            loadTasks(1); // Load initial tasks on page load

            // Function to load tasks using AJAX
            function loadTasks(page){
                $.ajax({
                    url: "load_tasks.php",
                    type: "GET",
                    data: { page: page },
                    success: function(response){
                        $("#task-list").html(response);
                    },
                    error: function(xhr, status, error){
                        console.error(xhr.responseText);
                    }
                });
            }

            // Event listener for pagination links
            $(document).on("click", ".pagination a", function(e){
                e.preventDefault();
                var page = $(this).attr("href").split("=")[1];
                loadTasks(page);
            });
        });
    </script>
</body>
</html>