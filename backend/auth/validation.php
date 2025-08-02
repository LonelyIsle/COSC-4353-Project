<?php
// backend/auth/validation.php
// Validation helpers for EMForm and VolunteerMatching

/**
 * Validate the POST data from EMForm.php
 *
 * @param array $data
 * @return string[]  list of error messages
 */
function validateEventData(array $data): array
{
    $errors = [];

    $name = trim($data['event_name'] ?? '');
    if ($name === '') {
        $errors[] = 'Event Name is required.';
    } elseif (strlen($name) > 100) {
        $errors[] = 'Event Name must be 100 characters or fewer.';
    }

    $desc = trim($data['event_description'] ?? '');
    if ($desc === '') {
        $errors[] = 'Description is required.';
    }

    $loc = trim($data['location'] ?? '');
    if ($loc === '') {
        $errors[] = 'Location is required.';
    }

    $skills = $data['required_skills'] ?? [];
    if (!is_array($skills) || count($skills) === 0) {
        $errors[] = 'Select at least one skill.';
    }

    $urgency = trim($data['urgency'] ?? '');
    if ($urgency === '') {
        $errors[] = 'Urgency level is required.';
    }

    $date = trim($data['event_date'] ?? '');
    if ($date === '') {
        $errors[] = 'Event date is required.';
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $errors[] = 'Event date must be in YYYY-MM-DD format.';
    }

    return $errors;
}

/**
 * Validate the POST data from VolunteerMatching.php
 *
 * @param array $data
 * @return string[]  list of error messages
 */
function validateMatchData(array $data): array
{
    $errors = [];

    $vol = trim($data['volunteer_name'] ?? '');
    if ($vol === '') {
        $errors[] = 'Volunteer is required.';
    }

    $evt = trim($data['matched_event'] ?? '');
    if ($evt === '') {
        $errors[] = 'Event selection is required.';
    }

    return $errors;
}
