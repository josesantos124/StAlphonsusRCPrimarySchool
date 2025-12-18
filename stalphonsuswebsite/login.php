<?php
// Start session to store user login data
session_start();

// Include database connection
require 'db.php';

// Ensure correct character encoding
header('Content-Type: text/html; charset=utf-8');

// Variables for error messages and form input
$error = "";
$username = "";

// Check if the login form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get and clean user input
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Basic validation
    if ($username === "" || $password === "") {
        $error = "Please enter both username and password.";
    } else {

        // Retrieve user record from the database using a prepared statement
        $stmt = $conn->prepare("SELECT * FROM login WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if exactly one matching user was found
        if ($result && $result->num_rows === 1) {
            $row = $result->fetch_assoc();

            // Verify the entered password against the stored hashed password
            if (password_verify($password, trim($row['password']))) {

                // Store important user details in session variables
                $_SESSION['loginID']  = $row['loginID'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role']     = $row['role'];
                $_SESSION['linkedID'] = $row['linkedID'];

                // Redirect user to the correct dashboard based on role
                switch ($row['role']) {
                    case 'admin':
                        header("Location: admin_dashboard.php");
                        exit;
                    case 'teacher':
                        header("Location: teacher_dashboard.php");
                        exit;
                    case 'student':
                        header("Location: student_dashboard.php");
                        exit;
                }

            } else {
                // Incorrect password
                $error = "Invalid username or password.";
            }
        } else {
            // No matching user found
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | St Alphonsus RC Primary School</title>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
body { background-color: #f7f8fc; color: #333; display: flex; flex-direction: column; min-height: 100vh; }
header { background-color: #004080; color: white; text-align: center; padding: 1rem 0; }
main { flex: 1; display: flex; justify-content: center; align-items: center; padding: 2rem; }

.form-container {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 400px;
}

form { display: flex; flex-direction: column; gap: 1rem; }
label { font-weight: 600; }
input { padding: 0.7rem; border: 1px solid #ccc; border-radius: 5px; }
button { background-color: #ffcc00; border: none; padding: 0.8rem;
         border-radius: 5px; cursor: pointer; font-weight: 600; }
button:hover { background-color: #ffdb4d; }

.error-message { text-align: center; color: red; font-weight: 600; margin-top: 1rem; }
a { color: #004080; text-decoration: none; font-weight: 600; }
a:hover { text-decoration: underline; }
</style>
</head>

<body>

<header>
<a href="index.php" style="text-decoration: none; color: white;">
    <h1>St Alphonsus RC Primary School</h1>
</a>
<p>Login Portal</p>
</header>

<main>
<div class="form-container">

<h2>Login</h2>

<!-- Login form -->
<form method="POST">

    <!-- Username input -->
    <label for="username">Username</label>
    <input type="text" id="username" name="username"
           value="<?= htmlspecialchars($username) ?>" required>

    <!-- Password input -->
    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <button type="submit">Login</button>
</form>

<!-- Display error message if login fails -->
<?php if ($error): ?>
<p class="error-message">❌ <?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<p style="text-align:center; margin-top:1rem;">
Not registered? <a href="signup.php">Sign Up Here</a>
</p>

</div>
</main>

<footer style="background:#004080; color:white; text-align:center; padding:1rem;">
<p>© 2025 St Alphonsus RC Primary School | All Rights Reserved</p>
</footer>

</body>
</html>
