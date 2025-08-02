<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// point at the same validation file
require_once __DIR__ . '/../backend/auth/validation.php';

class ValidateMatchDataTest extends TestCase
{
    public function testAllFieldsValid(): void
    {
        $post = [
            'volunteer_name' => 'Alice Smith',
            'matched_event'  => 'Community Cleanup',
        ];
        $errors = validateMatchData($post);
        $this->assertEmpty($errors, 'Expected no validation errors when all fields provided');
    }

    public function testMissingFields(): void
    {
        $post = [];
        $errors = validateMatchData($post);

        $this->assertContains('Volunteer is required.',      $errors);
        $this->assertContains('Event selection is required.', $errors);
        $this->assertCount(2, $errors);
    }
}
