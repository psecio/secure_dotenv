<?php
namespace Psecio\SecureDotenv;

use Psecio\SecureDotenv\KeySource\KeyFile;
use PHPUnit\Framework\TestCase;

class KeyFileTest extends TestCase
{
    public function testConstructorWithInvalidSource()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid source: invalid_source');

        new KeyFile('invalid_source');
    }
}
