<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            // Function to load content dynamically
            function loadContent(page) {
                $.ajax({
                    url: page,
                    success: function(response) {
                        $("#dynamic-content").html(response);
                    }
                });
            }

            // Initial load of default page
            loadContent("default.php");

            // Event listener for menu clicks
            $(".menu-item").click(function(){
                var page = $(this).attr("data-page");
                loadContent(page);
            });
        });
    </script>
</head>
<style>
    /* styles.css */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}

header {
    background-color: #333;
    color: #fff;
    padding: 10px 20px;
}

nav ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
}

nav ul li {
    display: inline-block;
    margin-right: 20px;
    cursor: pointer;
}

nav ul li:hover {
    color: #ffd700; /* Change color on hover */
}

#dynamic-content {
    padding: 20px;
}

h2 {
    color: #333;
}

p {
    color: #555;
}

/* Styling for forms */
form {
    max-width: 400px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

input[type="text"],
input[type="password"],
input[type="email"],
input[type="submit"] {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

input[type="submit"] {
    background-color: #4caf50;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #45a049;
}

/* Error message styling */
.error-message {
    color: #ff0000;
    margin-bottom: 10px;
}
</style>
<body>
    <!-- Header Section (Menu) -->
    <header>
        <nav>
            <ul>
                <?php
                session_start();
                // Check if user is logged in
                if (isset($_SESSION['user_id'])) {
                    // User is logged in, display menu items and welcome message
                    echo '<li>Welcome, ' . $_SESSION['username'] . '</li>';
                    echo '<li class="menu-item" data-page="task_manager.php">Task Manager</li>'; // Add Task Manager menu item
                    echo '<li><a href="logout.php">Logout</a></li>';
                } else {
                    // User is not logged in, display login and register links
                    echo '<li class="menu-item" data-page="default.php">Home</li>';
                    echo '<li class="menu-item" data-page="Register.html">Register</li>';
                    echo '<li class="menu-item" data-page="login.php">Login</li>';
                }
                ?>
            </ul>
        </nav>
    </header>

    <!-- Dynamic Content Section -->
    <div id="dynamic-content">
        <!-- Content will be loaded dynamically here -->
    </div>
</body>
</html>
