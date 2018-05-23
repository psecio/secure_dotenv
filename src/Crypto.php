<?php

namespace Psecio\SecureDotenv;

use Defuse\Crypto\Key as DefuseKey;
use Defuse\Crypto\Crypto as DefuseCrypto;

class Crypto
{
    private $key;

    public function __construct($key)
    {
        $this->setKey($this->createKey($key));
    }

    public function createKey($key)
    {
        if (is_file($key)) {
            $key = new KeySource\KeyFile($key);
        } else {
            $key = new KeySource\KeyString($key);
        }

        return $key;
    }

    public function setKey(KeySource $key)
    {
        $this->key = $key;
    }

    public function encrypt($value)
    {
        // Get the key contents, no sense in keeping it in memory for too long
        $keyAscii = trim($this->key->getContent());
        return DefuseCrypto::encrypt($value, DefuseKey::loadFromAsciiSafeString($keyAscii));
    }

    public function decrypt($value)
    {
        try {
            $keyAscii = trim($this->key->getContent());
            $value = DefuseCrypto::decrypt($value, DefuseKey::loadFromAsciiSafeString($keyAscii));
            
            return $value;
        } catch (\Defuse\Crypto\Exception\CryptoException $e) {
            // The value probably wasn't encrypted, move along...
            return null;
        }
    }
}