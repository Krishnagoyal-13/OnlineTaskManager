<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS file -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        /* Add CSS styles for sidebar and main content */
        .container {
            display: flex;
            align-items: flex-start; /* Align items to the top */
        }
        
        aside {
            width: 20%;
            height: 100vh; /* Full height of the viewport */
            background-color: #f2f2f2;
            padding: 20px;
        }
        
        main {
            flex: 1; /* Take up remaining space */
            padding: 20px;
        }
        
        .sidebar-menu {
            list-style-type: none;
            padding: 0;
        }
        
        .sidebar-menu li {
            margin-bottom: 10px;
        }
        
        .sidebar-menu li a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }
        
        .content {
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <!-- Container for sidebar and main content -->
    <div class="container">
        <!-- Sidebar Section -->
        <aside>
            <!-- Sidebar menu for task management -->
            <ul class="sidebar-menu">
                <li><a href="#" class="menu-link" data-page="task.php">Add Task</a></li>
                <li><a href="#" class="menu-link" data-page="viewtask.php">View Tasks</a></li>
                <li><a href="#" class="menu-link" data-page="update_task.php">Update Tasks</a></li>
            </ul>
        </aside>

        <!-- Main Content Section -->
        <main>
            <!-- Your main content here -->
            <div class="content">
                <h2>Task Manager</h2>
                <p>This is the task management page.</p>
                <!-- Add your task management forms, tables, etc. here -->
            </div>
        </main>
    </div>

    <script>
        $(document).ready(function() {
            // Event listener for menu clicks
            $(".menu-link").click(function(e) {
                e.preventDefault();
                var page = $(this).attr("data-page");
                // Load content dynamically without refreshing the page
                $.ajax({
                    url: page,
                    success: function(response) {
                        $(".content").html(response);
                    }
                });
            });
        });
    </script>
</body>
</html>
