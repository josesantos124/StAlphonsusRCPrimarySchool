<?php
// validation_classes.php

function validateClassData($classID, $className, $teacherID, $capacity, $classYear) {
    $errors = [];

    // Class ID must be numeric and positive
    if (!is_numeric($classID) || intval($classID) <= 0) {
        $errors[] = "Class ID must be a positive number.";
    }

    // Class Name: letters, numbers, spaces, apostrophes
    if (empty($className) || !preg_match("/^[a-zA-Z0-9\s']{2,100}$/", $className)) {
        $errors[] = "Class Name must be 2–100 characters (letters, numbers, spaces, apostrophes).";
    }

    // Teacher ID must be chosen and numeric
    if (!is_numeric($teacherID) || intval($teacherID) <= 0) {
        $errors[] = "You must select a valid Teacher ID.";
    }

    // Capacity validation
    if (!is_numeric($capacity) || intval($capacity) <= 0) {
        $errors[] = "Capacity must be a positive number.";
    } elseif (intval($capacity) > 40) {
        $errors[] = "Capacity cannot exceed 40 pupils.";
    }

    // Valid year groups
    $validYears = ["Reception", "Year 1", "Year 2", "Year 3", "Year 4", "Year 5", "Year 6"];

    if (empty($classYear) || !in_array($classYear, $validYears)) {
        $errors[] = "You must select a valid Class Year.";
    }

    return $errors;
}
?>
