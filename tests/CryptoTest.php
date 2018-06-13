<?php
namespace Psecio\SecureDotenv;

use Psecio\SecureDotenv\KeySource\KeyString;
use PHPUnit\Framework\TestCase;

class CryptoTest extends TestCase
{
    public function testSetKeyOnInit()
    {
        $keyString = '123456';
        $c = new Crypto($keyString);
        $k = $c->getKey();

        $this->assertInstanceOf(KeyString::class, $k);
        $this->assertEquals($k->getContent(), $keyString);
    }

    public function testGetSetKey()
    {
        $keyString = '123456';
        $c = new Crypto($keyString);

        $this->assertEquals($c->getKey()->getContent(), $keyString);
        
        // Reset it
        $key = new KeySource\KeyString('test123');
        $c->setKey($key);

        $this->assertEquals($c->getKey()->getContent(), 'test123');
    }

    public function testEncryptDecrypt()
    {
        $value = 'test1234';
        $c = new Crypto(__DIR__.'/test-encryption-key.txt');
        $encrypted = $c->encrypt($value);

        $this->assertNotEquals($value, $encrypted);
        $this->assertEquals($value, $c->decrypt($encrypted));

    }

    public function testDecryptWithInvalidValue()
    {
        $c = new Crypto(__DIR__.'/test-encryption-key.txt');

        $this->assertNull($c->decrypt('invalid_value'));
    }

    public function testCreateKeyWithInalvidKey()
    {
        $c = new Crypto(__DIR__.'/test-encryption-key.txt');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Could not create key from value provided.');
        $c->createKey(1000);
    }
}