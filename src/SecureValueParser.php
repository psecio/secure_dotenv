<?php

namespace Psecio\SecureDotenv;

class SecureValueParser extends \M1\Env\Parser\ValueParser
{
    private $crypto;

    public function __construct($parser, \Psecio\Securedotenv\Crypto $crypto)
    {
        $this->crypto = $crypto;

        parent::__construct($parser);
    }

    public function parse($value)
    {   
        $value = $this->crypto->decrypt(trim($value));
        return parent::parse($value);
    }
}