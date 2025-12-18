<?php
// validate_teacher.php

function validateTeacherData($teacherID, $teacherName, $teacherAddress, $teacherTelephone, $backgroundCheck, $salary) {
    $errors = [];

    // Teacher ID must be numeric and positive
    if (!is_numeric($teacherID) || intval($teacherID) <= 0) {
        $errors[] = "Teacher ID must be a positive number.";
    }

    // Teacher Name: letters, spaces, apostrophes, 2-100 characters
    if (empty($teacherName) || !preg_match("/^[a-zA-Z\s']{2,100}$/", $teacherName)) {
        $errors[] = "Teacher Name must be letters and spaces only, 2–100 characters.";
    }

    // Teacher Address: at least 5 characters
    if (empty($teacherAddress) || strlen($teacherAddress) < 5) {
        $errors[] = "Teacher Address must be at least 5 characters long.";
    }

    // Teacher Telephone: exactly 11 digits
    if (!preg_match("/^\d{11}$/", $teacherTelephone)) {
        $errors[] = "Teacher Telephone must be exactly 11 digits.";
    }

    // Background check field cannot be empty
    if (empty($backgroundCheck)) {
        $errors[] = "Background Check information is required.";
    }

    // Salary validation
    if (!is_numeric($salary) || floatval($salary) <= 0) {
        $errors[] = "Salary must be a positive number.";
    } elseif (floatval($salary) < 10000 || floatval($salary) > 100000) {
        $errors[] = "Salary must be between £10,000 and £100,000.";
    }

    return $errors;
}
?>
