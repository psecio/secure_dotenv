<?php
namespace Psecio\SecureDotenv;

use PHPUnit\Framework\TestCase;

class CryptoTest extends TestCase
{
    public function testSetKeyOnInit()
    {
        $keyString = '123456';
        $c = new Crypto($keyString);
        $k = $c->getKey();

        $this->assertInstanceOf('\\Psecio\\SecureDotenv\\KeySource\\KeyString', $k);
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

        $this->assertFalse($value == $encrypted);
        $this->assertEquals($value, $c->decrypt($encrypted));

    }
}