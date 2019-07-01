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

    public function testInvalidInit()
    {
        $this->expectException(\InvalidArgumentException::class);

        $keyString = new \stdClass();
        new KeyString($keyString);
    }
}
