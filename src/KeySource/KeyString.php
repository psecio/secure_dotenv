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
        if (!is_file($source)) {
            throw new \InvalidArgumentException('Invalid source: '.$source);
        }
        $this->setContents(file_get_contents($source));
    }
}