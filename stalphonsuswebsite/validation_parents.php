<?php
// validate_parents.php

function validateParentData($parentID, $parentName, $parentEmail, $parentAddress) {
    $errors = [];

    // Parent ID must be numeric and positive
    if (!is_numeric($parentID) || intval($parentID) <= 0) {
        $errors[] = "Parent ID must be a positive number.";
    }

    // Parent Name: letters, spaces, apostrophes, 2-100 characters
    if (empty($parentName) || !preg_match("/^[a-zA-Z\s']{2,100}$/", $parentName)) {
        $errors[] = "Parent Name must be letters and spaces only, 2–100 characters.";
    }

    // Parent Email: must be valid format
    if (empty($parentEmail) || !filter_var($parentEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid Email Address.";
    }

    // Parent Address: min 5 characters
    if (empty($parentAddress) || strlen($parentAddress) < 5) {
        $errors[] = "Parent Address must be at least 5 characters long.";
    }

    return $errors;
}
?>
