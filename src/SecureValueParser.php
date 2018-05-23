<?php

namespace Psecio\SecureDotenv;

/**
 * This class extends the ValueParser class from the M1\Env library and 
 * the "parse" method to automagically decrypt the value when it is requested
 */
class SecureValueParser extends \M1\Env\Parser\ValueParser
{
    /**
     * Current instance of the Crypto class
     *
     * @var \Psecio\SecureDotenv\Crypto
     */
    private $crypto;

    /**
     * Init the object and set the crypto and parser
     *
     * @param \Psecio\SecureDotenv\Parser $parser instance
     * @param \Psecio\Securedotenv\Crypto $crypto instance
     */
    public function __construct(\Psecio\SecureDotenv\Parser $parser, \Psecio\Securedotenv\Crypto $crypto)
    {
        $this->crypto = $crypto;

        parent::__construct($parser);
    }

    /**
     * Parse the value provided using the current Crypto instance
     *
     * @param string $value Ciphertext version of the value
     * @return string Decrypted value
     */
    public function parse($value)
    {   
        $value = $this->crypto->decrypt(trim($value));
        return parent::parse($value);
    }
}