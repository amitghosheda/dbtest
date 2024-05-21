<?php
// Database credentials
$db_host = 'localhost';
$db_user = 'amit';
$db_password = '1234';
$db_name = 'amit';

// Create database connection
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message and color variables
$message = "";
$color = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = $conn->real_escape_string($_POST["name"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $messageText = $conn->real_escape_string($_POST["message"]);

    // Validate form data
    if (empty($name) || empty($email) || empty($messageText)) {
        $message = "Please fill in all the fields.";
        $color = "red";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $color = "red";
    } else {
        // Insert data into the database
        $sql = "INSERT INTO contacts (name, email, message) VALUES ('$name', '$email', '$messageText')";

        if ($conn->query($sql) === TRUE) {
            $message = "Thank you for your message! We will get back to you soon.";
            $color = "green";
        } else {
            $message = "Oops! Something went wrong. Please try again later.";
            $color = "red";
        }
    }
}

// Close database connection
$conn->close();

// Send the message and color back to the HTML file
$response = array('message' => $message, 'color' => $color);
echo json_encode($response);
?>
