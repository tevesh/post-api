<?php
    
    namespace App\Tests;
    
    use PHPUnit\Framework\TestCase;
    
    class SimpleTest extends TestCase
    {
        /**
         * Show some basic assertion test
         */
        public function testAddition(): void
        {
            $value = true;
            $array = [
                'key' => 'value',
            ];
            // Check the true value of a variable
            $this->assertTrue($value);
            // Check that an array contain a certain key
            $this->assertArrayHasKey('key', $array);
            // Check the result of an operation ha the expected result
            $this->assertEquals(5, 2 + 3, 'Five was expected to equal 2+3');
            // Check if a value is equal than expected
            $this->assertEquals('value', $array['key']);
            // Check if an array contain exactly a certain number of objects
            $this->assertCount(1, $array);
        }
    }