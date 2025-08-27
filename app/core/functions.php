<?php
// Global helper functions will be added here.

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function validate_student_input($data) {
    $errors = [];

    if (empty($data['full_name'])) {
        $errors['full_name'] = 'Full name is required.';
    }

    if (empty($data['email'])) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    if (empty($data['username'])) {
        $errors['username'] = 'Username is required.';
    }

    if (empty($data['password'])) {
        $errors['password'] = 'Password is required.';
    }

    if (empty($data['class_id'])) {
        $errors['class_id'] = 'Class is required.';
    }

    if (empty($data['roll_number'])) {
        $errors['roll_number'] = 'Roll number is required.';
    }

    if (empty($data['admission_date'])) {
        $errors['admission_date'] = 'Admission date is required.';
    }

    return $errors;
}

function validate_teacher_input($data) {
    $errors = [];

    if (empty($data['full_name'])) {
        $errors['full_name'] = 'Full name is required.';
    }

    if (empty($data['email'])) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    if (empty($data['username'])) {
        $errors['username'] = 'Username is required.';
    }

    if (empty($data['password'])) {
        $errors['password'] = 'Password is required.';
    }

    if (empty($data['hire_date'])) {
        $errors['hire_date'] = 'Hire date is required.';
    }

    return $errors;
}

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

?>