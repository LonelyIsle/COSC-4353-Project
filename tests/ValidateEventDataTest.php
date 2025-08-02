<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// point at the new validation file
require_once __DIR__ . '/../backend/auth/validation.php';

class ValidateEventDataTest extends TestCase
{
    public function testAllFieldsValid(): void
    {
        $post = [
            'event_name'        => 'My Event',
            'event_description' => 'Something fun',
            'location'          => '123 Main St',
            'required_skills'   => ['Programming','Design'],
            'urgency'           => 'high',
            'event_date'        => '2025-08-01',
        ];

        $errors = validateEventData($post);
        $this->assertEmpty($errors, 'Expected no validation errors for valid input');
    }

    public function testMissingRequiredFields(): void
    {
        $post = []; // empty
        $errors = validateEventData($post);

        $this->assertContains('Event Name is required.',               $errors);
        $this->assertContains('Description is required.',              $errors);
        $this->assertContains('Location is required.',                 $errors);
        $this->assertContains('Select at least one skill.',            $errors);
        $this->assertContains('Urgency level is required.',            $errors);
        $this->assertContains('Event date is required.',               $errors);
        $this->assertCount(6, $errors);
    }

    public function testTooLongName(): void
    {
        $post = [
            'event_name'        => str_repeat('A', 101),
            'event_description' => 'D',
            'location'          => 'L',
            'required_skills'   => ['Teamwork'],
            'urgency'           => 'low',
            'event_date'        => '2025-07-20',
        ];
        $errors = validateEventData($post);
        $this->assertContains('Event Name must be 100 characters or fewer.', $errors);
    }

    public function testInvalidDateFormat(): void
    {
        $post = [
            'event_name'        => 'Test',
            'event_description' => 'D',
            'location'          => 'L',
            'required_skills'   => ['Teamwork'],
            'urgency'           => 'low',
            'event_date'        => '20/07/2025',
        ];
        $errors = validateEventData($post);
        $this->assertContains('Event date must be in YYYY-MM-DD format.', $errors);
    }
}
