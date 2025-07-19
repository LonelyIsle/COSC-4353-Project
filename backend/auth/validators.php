<?php

function sanitizeInput($key, $source) {
    return htmlspecialchars(trim($source[$key] ?? ''));
}

function isValidZip($zip) {
    return (bool) preg_match('/^\d{5}$/', $zip);
}

function isValidDate($date) {
    return (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $date);
}

function hasRequiredFields($data, $requiredFields) {
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            return false;
        }
    }
    return true;
}