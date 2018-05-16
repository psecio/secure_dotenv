<?php

namespace Psecio\SecureDotenv;

use Defuse\Crypto\Key as DefuseKey;
use Defuse\Crypto\Crypto as DefuseCrypto;

class Crypto
{
    private $keyPath;

    public function __construct($keyPath)
    {
        $this->setKeypath($keyPath);
    }

    public function setKeypath($keyPath)
    {
        if (empty($keyPath) || !is_file($keyPath)) {
            throw new \InvalidArgumentException('Invalid key file path: '.$keyPath);
        }
        $this->keyPath = $keyPath;
    }

    public function encrypt($value)
    {
        // Get the key contents, no sense in keeping it in memory for too long
        $keyAscii = trim(file_get_contents($this->keyPath));
        return DefuseCrypto::encrypt($value, DefuseKey::loadFromAsciiSafeString($keyAscii));
    }

    public function decrypt($value)
    {
        try {
            $keyAscii = trim(file_get_contents($this->keyPath));
            $value = DefuseCrypto::decrypt($value, DefuseKey::loadFromAsciiSafeString($keyAscii));
            
            return $value;
        } catch (\Defuse\Crypto\Exception\CryptoException $e) {
            // The value probably wasn't encrypted, move along...
            return null;
        }
    }
}