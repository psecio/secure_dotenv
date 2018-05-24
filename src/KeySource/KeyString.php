<?php

namespace Psecio\SecureDotenv\KeySource;

class KeyString extends \Psecio\SecureDotenv\KeySource
{
    /**
     * Init the object and read the file to get the key contents
     *
     * @param string $source File path for the key
     */
    public function __construct($source)
    {
        if (!is_string($source)) {
            throw new \InvalidArgumentException('Invalid source: '.print_r($source, true));
        }
        $this->setContent($source);
    }
}