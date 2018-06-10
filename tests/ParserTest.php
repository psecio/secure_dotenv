<?php
namespace Psecio\SecureDotenv;

use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function testConstructor()
    {
        $parser = new Parser(__DIR__.'/../test-encryptnio-key.txt', __DIR__.'/.env');

        $this->assertInstanceOf(Parser::class, $parser);
    }

    public function testSetConfigPathWithInvalidPath()
    {
        $parser = new Parser(__DIR__.'/../test-encryptnio-key.txt', __DIR__.'/.env');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid config file path: invalid_config_file');
        $parser->setConfigPath('invalid_config_file');
    }

    public function testSave()
    {
        $c = new Crypto(__DIR__.'/test-encryption-key.txt');
        $parser = new Parser(__DIR__.'/../test-encryptnio-key.txt', __DIR__.'/.env');
        $parser->setCrypto($c);

        $this->assertEquals(201, $parser->save('env1', 'test1234', true));
    }

    public function testWriteEnvWithDeplicatedEnv()
    {
        $c = new Crypto(__DIR__.'/test-encryption-key.txt');
        $parser = new Parser(__DIR__.'/../test-encryptnio-key.txt', __DIR__.'/.env');
        $parser->setCrypto($c);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Key name "env1" already exists!');
        $parser->writeEnv('env1', 'overwrite_value1');
    }
}
