<?php
// index.php
$success = false;
$errorMessages = [];

// Initialize form variables
$parentID = $parentName = $parentEmail = $parentAddress = "";

// Check for POST submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'validation_parents.php';

    // Capture POST data
    $postID        = $_POST['parentID'] ?? '';
    $postName      = $_POST['parentName'] ?? '';
    $postEmail     = $_POST['parentEmail'] ?? '';
    $postAddress   = $_POST['parentAddress'] ?? '';

    // Validate data
    $validationErrors = validateParentData($postID, $postName, $postEmail, $postAddress);

    if (empty($validationErrors)) {
        require 'db.php';

        // Escape inputs for security
        $parentID       = $conn->real_escape_string($postID);
        $parentName     = $conn->real_escape_string($postName);
        $parentEmail    = $conn->real_escape_string($postEmail);
        $parentAddress  = $conn->real_escape_string($postAddress);

        $sql = "INSERT INTO parents (parentID, parentName, parentEmail, parentAddress)
                VALUES ('$parentID', '$parentName', '$parentEmail', '$parentAddress')";

        if ($conn->query($sql) === TRUE) {
            $success = true;
            // Clear the form fields for new input
            $parentID = $parentName = $parentEmail = $parentAddress = "";
        } else {
            $errorMessages[] = "Database error: " . $conn->error;

            // Keep values so user can correct
            $parentID = $postID;
            $parentName = $postName;
            $parentEmail = $postEmail;
            $parentAddress = $postAddress;
        }
    } else {
        $errorMessages = $validationErrors;

        // Keep values so user can correct
        $parentID = $postID;
        $parentName = $postName;
        $parentEmail = $postEmail;
        $parentAddress = $postAddress;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Parent Registration | St Alphonsus RC Primary School</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
body { background-color: #f7f8fc; color: #333; display: flex; flex-direction: column; min-height: 100vh; }
header { background-color: #004080; color: white; text-align: center; padding: 1rem 0; }
header img { height: 60px; }
main { flex: 1; display: flex; justify-content: center; align-items: center; padding: 2rem; }
.form-container { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 600px; }
.form-container h2 { text-align: center; color: #004080; margin-bottom: 1.5rem; }
form { display: flex; flex-direction: column; gap: 1rem; }
label { font-weight: 600; }
input { width: 100%; padding: 0.7rem; border: 1px solid #ccc; border-radius: 5px; }
button { background-color: #ffcc00; border: none; padding: 0.8rem; border-radius: 5px; cursor: pointer; font-weight: 600; }
button:hover { background-color: #ffdb4d; }
footer { background-color: #004080; color: white; text-align: center; padding: 1rem 0; }
.success-message { text-align: center; color: green; font-weight: 600; margin-top: 1rem; }
.error-message { text-align: center; color: red; font-weight: 600; margin-top: 1rem; }
</style>
</head>
<body>

<header>
<a href="index.php" style="text-decoration: none; color: white;">
    <h1>St Alphonsus RC Primary School</h1>
</a>
<p>Parent Registration Form</p>
</header>

<main>
<div class="form-container">
<h2>Parent Registration</h2>

<form action="" method="POST">
    <div>
        <label for="parentID">Parent ID</label>
        <input type="number" id="parentID" name="parentID" value="<?= htmlspecialchars($parentID) ?>" required>
    </div>

    <div>
        <label for="parentName">Parent Name</label>
        <input type="text" id="parentName" name="parentName" value="<?= htmlspecialchars($parentName) ?>" required>
    </div>

    <div>
        <label for="parentEmail">Parent Email</label>
        <input type="email" id="parentEmail" name="parentEmail" value="<?= htmlspecialchars($parentEmail) ?>" required>
    </div>

    <div>
        <label for="parentAddress">Parent Address</label>
        <input type="text" id="parentAddress" name="parentAddress" value="<?= htmlspecialchars($parentAddress) ?>" required>
    </div>

    <button type="submit">Submit Registration</button>
</form>

<?php if ($success): ?>
<p class="success-message">✅ Thank you! The parent has been registered successfully.</p>
<?php elseif (!empty($errorMessages)): ?>
<div class="error-message">
<?php foreach ($errorMessages as $error): ?>
<p>❌ <?= htmlspecialchars($error) ?></p>
<?php endforeach; ?>
</div>
<?php endif; ?>

</div>
</main>

<footer>
<p>© 2025 St Alphonsus RC Primary School | All Rights Reserved</p>
</footer>

</body>
</html>
