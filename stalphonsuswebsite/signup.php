<?php
// Start session to manage user data across pages
session_start();

// Include database connection
require 'db.php';

// Variables for messages and form values
$error = "";
$success = "";
$loginID = "";
$username = "";
$role = "";

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data (use empty string if not set)
    $loginID = $_POST['studentID'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Remove extra spaces from input
    $loginID = trim($loginID);
    $username = trim($username);
    $password = trim($password);
    $role = trim($role);

    // Validate that all fields are filled
    if (empty($loginID) || empty($username) || empty($password) || empty($role)) {
        $error = "All fields are required.";

    // Ensure only valid roles can be selected
    } elseif (!in_array($role, ['admin', 'teacher', 'student'])) {
        $error = "Invalid role selected.";

    } else {
        // Check if username or login ID already exists
        $stmt = $conn->prepare("SELECT * FROM login WHERE username = ? OR loginID = ?");
        $stmt->bind_param("ss", $username, $loginID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username or Login ID already exists.";
        } else {
            // Hash the password for security
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into the database
            $stmt = $conn->prepare(
                "INSERT INTO login (loginID, username, password, role, linkedID)
                 VALUES (?, ?, ?, ?, NULL)"
            );
            $stmt->bind_param("ssss", $loginID, $username, $passwordHash, $role);

            // Execute and display feedback
            if ($stmt->execute()) {
                $success = "Account created successfully! You can now <a href='login.php'>login</a>.";
                // Clear fields after successful signup
                $loginID = $username = $role = "";
            } else {
                $error = "Database error: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign Up | St Alphonsus RC Primary School</title>

<style>
/* Basic page styling */
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
body { background-color: #f7f8fc; color: #333; display: flex; flex-direction: column; min-height: 100vh; }
header, footer { background-color: #004080; color: white; text-align: center; padding: 1rem 0; }
main { flex: 1; display: flex; justify-content: center; align-items: center; padding: 2rem; }

/* Form styling */
.form-container {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 400px;
}

form { display: flex; flex-direction: column; gap: 1rem; }
input, select { padding: 0.7rem; border: 1px solid #ccc; border-radius: 5px; }
button { background-color: #ffcc00; border: none; padding: 0.8rem; font-weight: 600; cursor: pointer; }
button:hover { background-color: #ffdb4d; }

.success-message { color: green; font-weight: 600; text-align: center; }
.error-message { color: red; font-weight: 600; text-align: center; }
</style>
</head>

<body>

<header>
    <h1>St Alphonsus RC Primary School</h1>
    <p>Create Your Account</p>
</header>

<main>
<div class="form-container">

<h2>Sign Up</h2>

<!-- Signup form -->
<form method="POST">

    <!-- Login ID -->
    <label>Login ID</label>
    <input type="text" name="studentID"
           value="<?= htmlspecialchars($loginID) ?>" required>

    <!-- Username -->
    <label>Username</label>
    <input type="text" name="username"
           value="<?= htmlspecialchars($username) ?>" required>

    <!-- Password -->
    <label>Password</label>
    <input type="password" name="password" required>

    <!-- Role selection -->
    <label>Role</label>
    <select name="role" required>
        <option value="">Select Role</option>
        <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
        <option value="teacher" <?= $role === 'teacher' ? 'selected' : '' ?>>Teacher</option>
        <option value="student" <?= $role === 'student' ? 'selected' : '' ?>>Student</option>
    </select>

    <button type="submit">Sign Up</button>
</form>

<!-- Error message -->
<?php if ($error): ?>
<p class="error-message"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<!-- Success message -->
<?php if ($success): ?>
<p class="success-message"><?= $success ?></p>
<?php endif; ?>

<p style="text-align:center; margin-top:1rem;">
Already have an account? <a href="login.php">Login here</a>
</p>

</div>
</main>

<footer>
<p>© 2025 St Alphonsus RC Primary School</p>
</footer>

</body>
</html>
