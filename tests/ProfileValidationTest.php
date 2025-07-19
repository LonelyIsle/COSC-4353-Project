<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../backend/auth/validators.php';

class ProfileValidationTest extends TestCase {

    public function testSanitizeInputStripsTags() {
        $data = ['name' => ' <script>evil()</script> '];
        $result = sanitizeInput('name', $data);
        $this->assertEquals('&lt;script&gt;evil()&lt;/script&gt;', $result);
    }

    public function testValidZipCode() {
        $this->assertTrue(isValidZip('12345'));
        $this->assertFalse(isValidZip('abc12'));
        $this->assertFalse(isValidZip('1234'));
    }

    public function testValidDateFormat() {
        $this->assertTrue(isValidDate('2025-07-18'));
        $this->assertFalse(isValidDate('07/18/2025'));
    }

    public function testRequiredFieldsPresent() {
        $data = ['full-name' => 'Jane', 'City' => 'Houston', 'State' => 'TX'];
        $this->assertTrue(hasRequiredFields($data, ['full-name', 'City', 'State']));
        $this->assertFalse(hasRequiredFields($data, ['full-name', 'Address1']));
    }
}