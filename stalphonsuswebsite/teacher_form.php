<?php
// index.php
$success = false;
$errorMessages = [];

// Initialize form variables
$teacherID = $teacherName = $teacherAddress = $teacherTelephone = $backgroundCheck = $salary = "";

// Check for POST submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'validation_teachers.php';

    // Capture POST data
    $postID        = $_POST['teacherID'] ?? '';
    $postName      = $_POST['teacherName'] ?? '';
    $postAddress   = $_POST['teacherAddress'] ?? '';
    $postTelephone = $_POST['teacherTelephone'] ?? '';
    $postBackgroundCheck = $_POST['backgroundCheck'] ?? '';
    $postSalary    = $_POST['salary'] ?? '';

    // Validate data
    $validationErrors = validateTeacherData($postID, $postName, $postAddress, $postTelephone, $postBackgroundCheck, $postSalary);

    if (empty($validationErrors)) {
        require 'db.php';

        // Escape inputs for security
        $teacherID        = $conn->real_escape_string($postID);
        $teacherName      = $conn->real_escape_string($postName);
        $teacherAddress   = $conn->real_escape_string($postAddress);
        $teacherTelephone = $conn->real_escape_string($postTelephone);
        $backgroundCheck  = $conn->real_escape_string($postBackgroundCheck);
        $salary           = $conn->real_escape_string($postSalary);

        $sql = "INSERT INTO teachers (teacherID, teacherName, teacherAddress, teacherTelephone, backgroundCheck, salary)
                VALUES ('$teacherID', '$teacherName', '$teacherAddress', '$teacherTelephone', '$backgroundCheck', '$salary')";

        if ($conn->query($sql) === TRUE) {
            $success = true;
            // Clear the form fields
            $teacherID = $teacherName = $teacherAddress = $teacherTelephone = $backgroundCheck = $salary = "";
        } else {
            $errorMessages[] = "Database error: " . $conn->error;

            // Keep values so user can correct
            $teacherID = $postID;
            $teacherName = $postName;
            $teacherAddress = $postAddress;
            $teacherTelephone = $postTelephone;
            $backgroundCheck = $postBackgroundCheck;
            $salary = $postSalary;
        }
    } else {
        // Validation failed
        $errorMessages = $validationErrors;

        // Keep values so user can correct
        $teacherID = $postID;
        $teacherName = $postName;
        $teacherAddress = $postAddress;
        $teacherTelephone = $postTelephone;
        $backgroundCheck = $postBackgroundCheck;
        $salary = $postSalary;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Teacher Registration | St Alphonsus RC Primary School</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
body { background-color: #f7f8fc; color: #333; display: flex; flex-direction: column; min-height: 100vh; }
header { background-color: #004080; color: white; text-align: center; padding: 1rem 0; }
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
<p>Teacher Registration Form</p>
</header>

<main>
<div class="form-container">
<h2>Teacher Registration</h2>

<form action="" method="POST">

    <div>
        <label for="teacherID">Teacher ID</label>
        <input type="number" id="teacherID" name="teacherID" value="<?= htmlspecialchars($teacherID) ?>" required>
    </div>

    <div>
        <label for="teacherName">Teacher Name</label>
        <input type="text" id="teacherName" name="teacherName" value="<?= htmlspecialchars($teacherName) ?>" required>
    </div>

    <div>
        <label for="teacherAddress">Teacher Address</label>
        <input type="text" id="teacherAddress" name="teacherAddress" value="<?= htmlspecialchars($teacherAddress) ?>" required>
    </div>

    <div>
        <label for="teacherTelephone">Teacher Telephone</label>
        <input type="text" id="teacherTelephone" name="teacherTelephone" value="<?= htmlspecialchars($teacherTelephone) ?>" required>
    </div>

    <div>
        <label for="backgroundCheck">Background Check</label>
        <input type="text" id="backgroundCheck" name="backgroundCheck" value="<?= htmlspecialchars($backgroundCheck) ?>" required>
    </div>

    <div>
        <label for="salary">Salary (£)</label>
        <input type="number" id="salary" name="salary" value="<?= htmlspecialchars($salary) ?>" required>
    </div>

    <button type="submit">Submit Registration</button>
</form>

<?php if ($success): ?>
<p class="success-message">✅ Thank you! The teacher has been registered successfully.</p>
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
