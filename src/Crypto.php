<?php

namespace Psecio\SecureDotenv;

use Defuse\Crypto\Key as DefuseKey;
use Defuse\Crypto\Crypto as DefuseCrypto;

class Crypto
{
    /**
     * Current key value (either File or String version)
     *
     * @var \Psecio\SecureDotenv\KeySource
     */
    private $key;

    /**
     * Init the object and set up the key
     *
     * @param string $key The "key" value, either a string or a file path
     */
    public function __construct($key)
    {
        $this->setKey($this->createKey($key));
    }

    /**
     * Create the key instance based on either a string or file path
     *
     * @param string $key The "key" value, either a string or a file path
     * @return \Psecio\SecureDotenv\KeySource instance
     */
    public function createKey($key)
    {
        if (is_file($key)) {
            $key = new KeySource\KeyFile($key);
        } else {
            $key = new KeySource\KeyString($key);
        }

        return $key;
    }

    /**
     * Set the currekt key instance
     *
     * @param KeySource $key instance
     */
    public function setKey(KeySource $key)
    {
        $this->key = $key;
    }

    /**
     * Encrypt the value provided with the current key and the Defuse library
     *
     * @param string $value Value to encrypt
     * @return string Ciphertext (encrypted) value
     */
    public function encrypt($value)
    {
        // Get the key contents, no sense in keeping it in memory for too long
        $keyAscii = trim($this->key->getContent());
        return DefuseCrypto::encrypt($value, DefuseKey::loadFromAsciiSafeString($keyAscii));
    }

    /**
     * Decrypt the ciphertext value provided
     * This method also catches values that may not be encrypted 
     * and returns them normally
     * 
     * @param string $value Ciphertext (encrypted) string
     * @return mixed The value if it could be decrypted, otherwse null
     */
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