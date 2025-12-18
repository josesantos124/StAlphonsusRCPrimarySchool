<?php
$success = false;
$errorMessages = [];

// Form variables
$classID = $className = $teacherID = $capacity = $classYear = "";

// POST submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'validation_classes.php';
    require 'db.php';

    // Get POST values
    $postClassID   = $_POST['classID'] ?? '';
    $postClassName = $_POST['className'] ?? '';
    $postTeacherID = $_POST['teacherID'] ?? '';
    $postcapacity  = $_POST['capacity'] ?? '';
    $postclassYear = $_POST['classYear'] ?? '';

    // Validate
    $validationErrors = validateClassData($postClassID, $postClassName, $postTeacherID, $postcapacity, $postclassYear);

    if (empty($validationErrors)) {

        // Escape inputs
        $classID    = $conn->real_escape_string($postClassID);
        $className  = $conn->real_escape_string($postClassName);
        $teacherID  = $conn->real_escape_string($postTeacherID);
        $capacity   = $conn->real_escape_string($postcapacity);
        $classYear  = $conn->real_escape_string($postclassYear);

        // Insert
        $sql = "INSERT INTO classes (classID, className, teacherID, capacity, classYear)
                VALUES ('$classID', '$className', '$teacherID', '$capacity', '$classYear')";

        if ($conn->query($sql) === TRUE) {
            $success = true;
            // Clear form
            $classID = $className = $teacherID = $capacity = $classYear = "";
        } else {
            $errorMessages[] = "Database error: " . $conn->error;
            $classID = $postClassID;
            $className = $postClassName;
            $teacherID = $postTeacherID;
            $capacity  = $postcapacity;
            $classYear = $postclassYear;
        }

    } else {
        $errorMessages = $validationErrors;
        $classID = $postClassID;
        $className = $postClassName;
        $teacherID = $postTeacherID;
        $capacity  = $postcapacity;
        $classYear = $postclassYear;
    }
}

// Load teachers for dropdown
require 'db.php';
$teachers = $conn->query("SELECT teacherID, teacherName FROM teachers");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Class Registration | St Alphonsus RC Primary School</title>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
body { background-color: #f7f8fc; color: #333; display: flex; flex-direction: column; min-height: 100vh; }
header { background-color: #004080; color: white; text-align: center; padding: 1rem 0; }
main { flex: 1; display: flex; justify-content: center; align-items: center; padding: 2rem; }
.form-container { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 600px; }
.form-container h2 { text-align: center; color: #004080; margin-bottom: 1.5rem; }
form { display: flex; flex-direction: column; gap: 1rem; }
label { font-weight: 600; }
input, select { width: 100%; padding: 0.7rem; border: 1px solid #ccc; border-radius: 5px; }
button { background-color: #ffcc00; border: none; padding: 0.8rem; border-radius: 5px; cursor: pointer; font-weight: 600; }
button:hover { background-color: #ffdb4d; }
.success-message { text-align: center; color: green; font-weight: 600; margin-top: 1rem; }
.error-message { text-align: center; color: red; font-weight: 600; margin-top: 1rem; }
footer { background-color: #004080; color: white; text-align: center; padding: 1rem 0; }
</style>
</head>

<body>

<header>
<a href="index.php" style="text-decoration: none; color: white;">
    <h1>St Alphonsus RC Primary School</h1>
</a>
<p>Class Registration Form</p>
</header>

<main>
<div class="form-container">
<h2>Register a Class</h2>

<form action="" method="POST">

    <div>
        <label for="classID">Class ID</label>
        <input type="number" id="classID" name="classID" value="<?= htmlspecialchars($classID) ?>" required>
    </div>

    <div>
        <label for="className">Class Name</label>
        <input type="text" id="className" name="className" value="<?= htmlspecialchars($className) ?>" required>
    </div>

    <div>
        <label for="teacherID">Teacher</label>
        <select name="teacherID" id="teacherID" required>
            <option value="">-- Select Teacher --</option>
            <?php while ($t = $teachers->fetch_assoc()): ?>
                <option value="<?= $t['teacherID'] ?>" <?= ($teacherID == $t['teacherID']) ? "selected" : "" ?>>
                    <?= $t['teacherID'] . " - " . htmlspecialchars($t['teacherName']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div>
        <label for="classYear">Class Year</label>
        <select name="classYear" id="classYear" required>
            <option value="">-- Select Year Group --</option>
            <?php
            $years = ["Reception", "Year 1", "Year 2", "Year 3", "Year 4", "Year 5", "Year 6"];
            foreach ($years as $year):
            ?>
                <option value="<?= $year ?>" <?= ($classYear == $year) ? "selected" : "" ?>>
                    <?= $year ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="capacity">Class Capacity</label>
        <input type="number" id="capacity" name="capacity" value="<?= htmlspecialchars($capacity) ?>" required>
    </div>

    <button type="submit">Submit Class</button>
</form>

<?php if ($success): ?>
<p class="success-message">✅ Class has been registered successfully.</p>

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
