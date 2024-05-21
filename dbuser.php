<?php
$db_created = false;
$user_created = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $dbname = $_POST['dbname'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Connect to MySQL (assuming default localhost setup)
    $mysqli = new mysqli("localhost", "root", ""); // Update with your MySQL credentials

    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Check if the database already exists
    $check_db_sql = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'";
    $db_result = $mysqli->query($check_db_sql);
    // Check if the user already exists
    $check_user_sql = "SELECT User FROM mysql.user WHERE User='$username'";
    $user_result = $mysqli->query($check_user_sql);

    if (($db_result && $db_result->num_rows > 0) || ($user_result && $user_result->num_rows > 0)) {
        echo "Warning: Database or User already exists.";
    } else {
        // Create the database
        $create_db_sql = "CREATE DATABASE $dbname";
        if ($mysqli->query($create_db_sql) === TRUE) {
            $db_created = true;
        } else {
            echo "Error creating database: " . $mysqli->error;
        }
        
        // Create the user
        $create_user_sql = "CREATE USER '$username'@'localhost' IDENTIFIED BY '$password'";
        if ($mysqli->query($create_user_sql) === TRUE) {
            $user_created = true;
        } else {
            echo "Error creating user: " . $mysqli->error;
        }

        // Grant privileges to the user on the database
        $grant_privileges_sql = "GRANT ALL PRIVILEGES ON $dbname.* TO '$username'@'localhost'";
        if ($mysqli->query($grant_privileges_sql) === TRUE) {
            echo "Privileges granted successfully. ";
        } else {
            echo "Error granting privileges: " . $mysqli->error;
        }
    }

    // Close MySQL connection
    $mysqli->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Database and User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 400px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        h2 {
            text-align: center;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 3px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .login-btn {
            display: <?php echo ($db_created && $user_created) ? 'block' : 'none'; ?>;
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 3px;
            background-color: #28a745;
            color: #fff;
            cursor: pointer;
            text-align: center;
            margin-top: 10px;
        }
        .login-btn a {
            color: #fff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Create Database and User</h2>
        <?php if ($db_created && $user_created): ?>
        <button class="login-btn"><a href="http://localhost/phpmyadmin" target="_blank">Login to PHPMyAdmin</a></button>
        <?php endif; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <label for="dbname">Database Name:</label>
            <input type="text" id="dbname" name="dbname" required><br>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>
            <input type="submit" value="Submit">
        </form>
    </div>
</body>
</html>
