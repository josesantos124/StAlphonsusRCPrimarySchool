<?php
session_start();
require 'db.php';

// Fetch all table names in the database
$tables = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

$queryResult = null;
$error = "";
$success = "";

// Handle record deletion
if (isset($_POST['delete_row']) && isset($_POST['table']) && isset($_POST['primary_key']) && isset($_POST['pk_value'])) {
    $table = $_POST['table'];
    $primary_key = $_POST['primary_key'];
    $pk_value = $_POST['pk_value'];

    // Delete record safely
    $sql = "DELETE FROM `$table` WHERE `$primary_key` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $pk_value);

    if ($stmt->execute()) {
        $success = "Record deleted successfully.";
    } else {
        $error = "Error deleting record.";
    }
}

// Handle search form
if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST['delete_row'])) {
    $table = $_POST['table'] ?? '';
    $column = $_POST['column'] ?? '';
    $operator = $_POST['operator'] ?? '';
    $value = $_POST['value'] ?? '';

    if ($table) {

        if (!in_array($table, $tables)) {
            $error = "Invalid table selected.";
        } else {
            $colRes = $conn->query("SHOW COLUMNS FROM `$table`");
            $validCols = [];
            while ($c = $colRes->fetch_assoc()) {
                $validCols[] = $c['Field'];
            }

            if ($column && in_array($column, $validCols) && $operator && $value !== '') {
                $sql = "SELECT * FROM `$table` WHERE `$column` $operator ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $value);
                $stmt->execute();
                $queryResult = $stmt->get_result();
            } else {
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
<title>Admin Dashboard</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
body { background-color: #f7f8fc; color: #333; display: flex; flex-direction: column; min-height: 100vh; }
header { background-color: #004080; color: white; text-align: center; padding: 1rem 0; }
main { flex: 1; display: flex; justify-content: center; padding: 2rem; }
.form-container { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 800px; }
h2 { text-align: center; color: #004080; margin-bottom: 1.5rem; }
form { display: flex; flex-direction: column; gap: 1rem; }
label { font-weight: 600; }
select, input { width: 100%; padding: 0.7rem; border: 1px solid #ccc; border-radius: 5px; }
button { background-color: #ffcc00; border: none; padding: 0.8rem; border-radius: 5px; cursor: pointer; font-weight: 600; }
button:hover { background-color: #ffdb4d; }
.error-message { color: red; text-align: center; margin-top: 1rem; }
.success-message { color: green; text-align: center; margin-top: 1rem; }
.results-container { margin-top: 2rem; }
table { width: 100%; border-collapse: collapse; margin-top: 1rem; background: white; }
th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
th { background-color: #004080; color: white; }
.delete-btn { background-color: red; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; }
.delete-btn:hover { background-color: darkred; }
</style>
</head>
<body>

<header>
    <h1>Admin Dashboard</h1>
    <p>Welcome <?= htmlspecialchars($_SESSION['username'] ?? '') ?></p>
</header>

<main>
<div class="form-container">
    <h2>Database Query Tool</h2>

    <form method="POST">
        <label>Table (required):</label>
        <select name="table" required onchange="this.form.submit()">
            <option value="">Select a table</option>
            <?php foreach ($tables as $tbl): ?>
            <option value="<?= $tbl ?>" <?= isset($_POST['table']) && $_POST['table'] === $tbl ? 'selected' : '' ?>><?= $tbl ?></option>
            <?php endforeach; ?>
        </select>

        <?php if (!empty($_POST['table'])): ?>
            <?php $table = $_POST['table']; $cols = $conn->query("SHOW COLUMNS FROM `$table`"); ?>

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

    <?php if ($error): ?><p class="error-message"><?= $error ?></p><?php endif; ?>
    <?php if ($success): ?><p class="success-message"><?= $success ?></p><?php endif; ?>

    <?php if ($queryResult): ?>
    <div class="results-container">
        <h3>Results</h3>
        <table>
            <tr>
                <?php
                $fields = $queryResult->fetch_fields();
                foreach ($fields as $field): ?>
                    <th><?= $field->name ?></th>
                <?php endforeach; ?>
                <th>Delete</th>
            </tr>

            <?php
            $primary_key = $fields[0]->name; //  first column is primary key
            while ($row = $queryResult->fetch_assoc()): ?>
            <tr>
                <?php foreach ($row as $value): ?>
                    <td><?= htmlspecialchars($value) ?></td>
                <?php endforeach; ?>

                <td>
                    <form method="POST" style="display:inline-block;">
                        <input type="hidden" name="table" value="<?= $table ?>">
                        <input type="hidden" name="primary_key" value="<?= $primary_key ?>">
                        <input type="hidden" name="pk_value" value="<?= $row[$primary_key] ?>">
                        <button class="delete-btn" name="delete_row" onclick="return confirm('Delete this record?');">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <?php endif; ?>

</div>
</main>

<footer>
    <p>&copy; <?= date('Y') ?> Admin Panel</p>
</footer>

</body>
</html>
