<?php
//this is for testing VolunteerMatching.php backend

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../backend/auth/validation.php';

class ValidateMatchDataTest extends TestCase
{
    public function testAllFieldsValid()
    {
        $post = [
            'volunteer_name' => 'Alice Smith',
            'matched_event'  => 'Community Cleanup',
        ];
        $errors = validateMatchData($post);
        $this->assertEmpty($errors);
    }

    public function testMissingFields()
    {
        $post = [];
        $errors = validateMatchData($post);

        $this->assertContains('Volunteer is required.',     $errors);
        $this->assertContains('Event selection is required.', $errors);
        $this->assertCount(2, $errors);
    }
}
