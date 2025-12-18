<?php
// Start session to access logged-in user data
session_start();

// Include database connection
require 'db.php';

// Define which database tables teachers are allowed to access
$allowedTables = ["students", "parents"];

// Variables to store query results and errors
$queryResult = null;
$error = "";

// Only display allowed tables in the dropdown
$tables = $allowedTables;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Collect form inputs safely
    $table = $_POST['table'] ?? '';
    $column = $_POST['column'] ?? '';
    $operator = $_POST['operator'] ?? '';
    $value = $_POST['value'] ?? '';

    if ($table) {

        // Prevent access to unauthorised tables
        if (!in_array($table, $allowedTables)) {
            $error = "Access denied.";

        } else {
            // Fetch valid column names for the selected table
            $colRes = $conn->query("SHOW COLUMNS FROM `$table`");
            $validCols = [];

            while ($c = $colRes->fetch_assoc()) {
                $validCols[] = $c['Field'];
            }

            // Apply filtering only if column, operator, and value are valid
            if ($column && in_array($column, $validCols) && $operator && $value !== '') {

                // Use prepared statement to prevent SQL injection
                $sql = "SELECT * FROM `$table` WHERE `$column` $operator ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $value);
                $stmt->execute();
                $queryResult = $stmt->get_result();

            } else {
                // No filters selected → display entire table
                $sql = "SELECT * FROM `$table`";
                $queryResult = $conn->query($sql);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Teacher Dashboard</title>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
body { background-color: #f7f8fc; color: #333; display: flex; flex-direction: column; min-height: 100vh; }
header { background-color: #004080; color: white; text-align: center; padding: 1rem 0; }
main { flex: 1; display: flex; justify-content: center; align-items: flex-start; padding: 2rem; }
.form-container { background: white; padding: 2rem; border-radius: 10px;
                  box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 600px; }
form { display: flex; flex-direction: column; gap: 1rem; }
label { font-weight: 600; }
select, input { padding: 0.7rem; border: 1px solid #ccc; border-radius: 5px; }
button { background-color: #ffcc00; border: none; padding: 0.8rem; font-weight: 600; cursor: pointer; }
button:hover { background-color: #ffdb4d; }
.error-message { text-align: center; color: red; font-weight: 600; margin-top: 1rem; }

table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
th { background-color: #004080; color: white; }
</style>

</head>
<body>

<header>
    <h1>Teacher Dashboard</h1>
    <!-- Display logged-in teacher username -->
    <p>Welcome <?= htmlspecialchars($_SESSION['username'] ?? '') ?></p>
</header>

<main>
<div class="form-container">

<h2>View Student & Parent Data</h2>

<!-- Search and filter form -->
<form method="POST">

    <!-- Table selection (restricted to allowed tables) -->
    <label>Table (required):</label>
    <select name="table" required onchange="this.form.submit()">
        <option value="">Select a table</option>
        <?php foreach ($tables as $tbl): ?>
            <option value="<?= $tbl ?>"
                <?= (isset($_POST['table']) && $_POST['table'] == $tbl) ? 'selected' : '' ?>>
                <?= ucfirst($tbl) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- Show filters only after table is selected -->
    <?php if (!empty($_POST['table'])): ?>
        <?php $cols = $conn->query("SHOW COLUMNS FROM `{$_POST['table']}`"); ?>

        <label>Column (optional):</label>
        <select name="column">
            <option value="">-- None --</option>
            <?php while ($c = $cols->fetch_assoc()): ?>
                <option value="<?= $c['Field'] ?>"><?= $c['Field'] ?></option>
            <?php endwhile; ?>
        </select>

        <label>Operator (optional):</label>
        <select name="operator">
            <option value="">-- None --</option>
            <option value="=">=</option>
            <option value="LIKE">LIKE</option>
            <option value=">">></option>
            <option value="<"><</option>
            <option value=">=">>=</option>
            <option value="<="><=</option>
        </select>

        <label>Value (optional):</label>
        <input type="text" name="value">
    <?php endif; ?>

    <button type="submit">Search</button>
</form>

<!-- Error message -->
<?php if ($error): ?>
    <p class="error-message"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<!-- Display query results -->
<?php if ($queryResult): ?>
    <table>
        <tr>
            <?php foreach ($queryResult->fetch_fields() as $field): ?>
                <th><?= $field->name ?></th>
            <?php endforeach; ?>
        </tr>

        <?php while ($row = $queryResult->fetch_assoc()): ?>
        <tr>
            <?php foreach ($row as $value): ?>
                <td><?= htmlspecialchars($value) ?></td>
            <?php endforeach; ?>
        </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>

</div>
</main>

<footer style="text-align:center; padding:1rem; background:#004080; color:white;">
    <p>&copy; <?= date("Y") ?> Teacher Panel</p>
</footer>

</body>
</html>
