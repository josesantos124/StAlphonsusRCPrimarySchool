<?php
// index.php
$success = false;
$errorMessages = [];

// Initialize form variables
$studentID = $studentName = $studentAddress = $studentTelephone = $medicalInfo = "";
$parentID = $classID = "";

// Check for POST submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'validation_students.php';
    require 'db.php';

    // Capture POST data
    $postID          = $_POST['studentID'] ?? '';
    $postName        = $_POST['studentName'] ?? '';
    $postAddress     = $_POST['studentAddress'] ?? '';
    $postTelephone   = $_POST['studentTelephone'] ?? '';
    $postMedicalInfo = $_POST['medicalInfo'] ?? '';
    $postParentID    = $_POST['parentID'] ?? '';
    $postClassID     = $_POST['classID'] ?? '';

    // Validate student data
    $validationErrors = validateStudentData(
    $postID,
    $postName,
    $postAddress,
    $postTelephone,
    $postMedicalInfo
);


    // Additional validation
    if (empty($postMedicalInfo)) {
        $validationErrors[] = "Medical information must be provided.";
    }

    if (empty($postParentID)) {
        $validationErrors[] = "Parent must be selected.";
    }

    if (empty($postClassID)) {
        $validationErrors[] = "Class must be selected.";
    }

    if (empty($validationErrors)) {

        // ------------------------------
        // INSERT INTO students TABLE
        // ------------------------------
        $stmt = $conn->prepare(
            "INSERT INTO students (studentID, studentName, studentAddress, studentTelephone, medicalInfo)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("issss", $postID, $postName, $postAddress, $postTelephone, $postMedicalInfo);

        if ($stmt->execute()) {

            // -------------------------------------
            // INSERT INTO studentparent TABLE
            // -------------------------------------
            $stmt2 = $conn->prepare(
                "INSERT INTO studentparent (studentID, parentID)
                 VALUES (?, ?)"
            );
            $stmt2->bind_param("ii", $postID, $postParentID);
            $stmt2->execute();
            $stmt2->close();

            // -------------------------------------
            // INSERT INTO studentclasses TABLE
            // -------------------------------------
            $stmt3 = $conn->prepare(
                "INSERT INTO studentclasses (studentID, classID)
                 VALUES (?, ?)"
            );
            $stmt3->bind_param("ii", $postID, $postClassID);
            $stmt3->execute();
            $stmt3->close();

            $success = true;

            // Reset fields
            $studentID = $studentName = $studentAddress = $studentTelephone = $medicalInfo = "";
            $parentID = $classID = "";

        } else {
            $errorMessages[] = "Database error: " . $stmt->error;

            // Keep values if error
            $studentID = $postID;
            $studentName = $postName;
            $studentAddress = $postAddress;
            $studentTelephone = $postTelephone;
            $medicalInfo = $postMedicalInfo;
            $parentID = $postParentID;
            $classID = $postClassID;
        }

        $stmt->close();

    } else {
        $errorMessages = $validationErrors;

        // Keep values
        $studentID = $postID;
        $studentName = $postName;
        $studentAddress = $postAddress;
        $studentTelephone = $postTelephone;
        $medicalInfo = $postMedicalInfo;
        $parentID = $postParentID;
        $classID = $postClassID;
    }
}

// -------------------------------------------------------
// GET LIST OF PARENTS FOR DROPDOWN
// -------------------------------------------------------
require 'db.php';
$parentsResult = $conn->query("SELECT parentID, parentName FROM parents ORDER BY parentName");

// -------------------------------------------------------
// GET LIST OF CLASSES FOR DROPDOWN
// -------------------------------------------------------
$classesResult = $conn->query("SELECT classID, className FROM classes ORDER BY className");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Registration | St Alphonsus RC Primary School</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
body { background-color: #f7f8fc; color: #333; display: flex; flex-direction: column; min-height: 100vh; }
header { background-color: #004080; color: white; text-align: center; padding: 1rem 0; }
main { flex: 1; display: flex; justify-content: center; align-items: center; padding: 2rem; }
.form-container { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 600px; }
.form-container h2 { text-align: center; color: #004080; margin-bottom: 1.5rem; }
form { display: flex; flex-direction: column; gap: 1rem; }
label { font-weight: 600; }
input, select, textarea { width: 100%; padding: 0.7rem; border: 1px solid #ccc; border-radius: 5px; }
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
<p>Student Registration Form</p>
</header>

<main>
<div class="form-container">
<h2>Student Registration</h2>

<form action="" method="POST">

    <div>
        <label for="studentID">Student ID</label>
        <input type="number" id="studentID" name="studentID" value="<?= htmlspecialchars($studentID) ?>" required>
    </div>

    <div>
        <label for="studentName">Student Name</label>
        <input type="text" id="studentName" name="studentName" value="<?= htmlspecialchars($studentName) ?>" required>
    </div>

    <div>
        <label for="studentAddress">Student Address</label>
        <input type="text" id="studentAddress" name="studentAddress" value="<?= htmlspecialchars($studentAddress) ?>" required>
    </div>

    <div>
        <label for="studentTelephone">Student Telephone</label>
        <input type="text" id="studentTelephone" name="studentTelephone" value="<?= htmlspecialchars($studentTelephone) ?>" required>
    </div>

    <div>
        <label for="medicalInfo">Medical Information</label>
        <textarea id="medicalInfo" name="medicalInfo" rows="3" required><?= htmlspecialchars($medicalInfo) ?></textarea>
    </div>

    <div>
        <label for="parentID">Select Parent / Guardian</label>
        <select id="parentID" name="parentID" required>
            <option value="">-- Choose Parent --</option>
            <?php while ($row = $parentsResult->fetch_assoc()): ?>
                <option value="<?= $row['parentID'] ?>" <?= ($parentID == $row['parentID']) ? "selected" : "" ?>>
                    <?= htmlspecialchars($row['parentName']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div>
        <label for="classID">Select Class</label>
        <select id="classID" name="classID" required>
            <option value="">-- Choose Class --</option>
            <?php while ($row = $classesResult->fetch_assoc()): ?>
                <option value="<?= $row['classID'] ?>" <?= ($classID == $row['classID']) ? "selected" : "" ?>>
                    <?= htmlspecialchars($row['className']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <button type="submit">Submit Registration</button>
</form>

<?php if ($success): ?>
<p class="success-message">✅ Student has been registered successfully.</p>
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
