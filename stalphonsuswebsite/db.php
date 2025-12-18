<?php
// -------------------------------
// Database connection details
// -------------------------------

// The server where the database is hosted (localhost = same computer)
$host = 'localhost';

// Username used to access the database
$user = 'root';

// Password for the database  (empty by default in XAMPP)
$password = '';

// Name of the database the website is connected to
$database = 'stalphonsusdb';

// -------------------------------
// Create a database connection
// -------------------------------

// Creates a MySQLi connection using the details above
$conn = new mysqli($host, $user, $password, $database);

// -------------------------------
// Check if the connection failed
// -------------------------------

// If there is a connection error, stop the script and display an error message
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// If no error occurs, the connection is successful
?>


