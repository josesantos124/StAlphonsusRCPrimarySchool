<?php
// Start the session to access logged-in user data
session_start();

// Ensure the user is logged in AND has the student role
if (!isset($_SESSION['loginID']) || $_SESSION['role'] !== 'student') {
    // Redirect unauthorised users to the login page
    header("Location: login.php");
    exit();
}

// Include database connection
require 'db.php';

// Get user details from the session
$loginID = $_SESSION['loginID'];
$username = $_SESSION['username'];

// Retrieve student account information from the database
$stmt = $conn->prepare(
    "SELECT loginID, username, role FROM login WHERE loginID = ?"
);
$stmt->bind_param("s", $loginID);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Student Dashboard</title>

<style>
/* Basic page styling */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
}

header {
    background-color: #004080;
    color: white;
    padding: 20px;
    text-align: center;
}

header a {
    text-decoration: none;
    color: white;
}

/* Dashboard layout */
.dashboard {
    width: 50%;
    margin: 40px auto;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px #aaa;
}

.dashboard h2 {
    text-align: center;
}
</style>
</head>

<body>

<header>
    <a href="index.php">
        <h1>St Alphonsus RC Primary School</h1>
    </a>
    <p>Student Dashboard</p>
</header>

<div class="dashboard">

    <!-- Display student welcome message -->
    <h2>Welcome, <?= htmlspecialchars($student['username']) ?>!</h2>

    <!-- Display student account details -->
    <p><strong>Login ID:</strong> <?= htmlspecialchars($student['loginID']) ?></p>
    <p><strong>Username:</strong> <?= htmlspecialchars($student['username']) ?></p>
    <p><strong>Role:</strong> <?= htmlspecialchars($student['role']) ?></p>

    <br>

    <!-- Logout link ends the session -->
    <a href="logout.php">Logout</a>

</div>

</body>
</html>
