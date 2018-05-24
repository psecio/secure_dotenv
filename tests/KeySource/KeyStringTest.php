<?php
namespace Psecio\SecureDotenv\KeySource;

use PHPUnit\Framework\TestCase;

class KeyStringTest extends TestCase
{
    public function testValidInit()
    {
        $keyString = 'test1234';
        $key = new KeyString($keyString);

        $this->assertEquals($keyString, $key->getContent());
    }

    /**
     * Test what happens when a non-string is passed to the constructor
     *
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidInit()
    {
        $keyString = new \stdClass();
        $key = new KeyString($keyString);   
    }
}