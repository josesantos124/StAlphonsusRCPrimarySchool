<?php

function validateStudentData($studentID, $studentName, $studentAddress, $studentTelephone, $medicalInfo) {
    $errors = [];

    // Student ID must be numeric and positive
    if (!is_numeric($studentID) || intval($studentID) <= 0) {
        $errors[] = "Student ID must be a positive number.";
    }

    // Student Name: letters, spaces, apostrophes, 2-100 characters
    if (empty($studentName) || !preg_match("/^[a-zA-Z\s']{2,100}$/", $studentName)) {
        $errors[] = "Student Name must be letters and spaces only, 2-100 characters.";
    }

    // Student Address: at least 5 characters
    if (empty($studentAddress) || strlen($studentAddress) < 5) {
        $errors[] = "Student Address must be at least 5 characters long.";
    }

    // Student Telephone: exactly 11 digits
    if (!preg_match("/^\d{11}$/", $studentTelephone)) {
        $errors[] = "Student Telephone must be exactly 11 digits.";
    }

    // Medical Info
    if (empty($medicalInfo) || strlen($medicalInfo) < 5) {
        $errors[] = "Medical Information must be at least 5 characters long.";
    }

    return $errors;
}
