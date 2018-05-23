<?php

namespace Psecio\SecureDotenv\KeySource;

class KeyString extends \Psecio\SecureDotenv\KeySource
{
    public function __construct($source)
    {
        if (!is_file($source)) {
            throw new \InvalidArgumentException('Invalid source: '.$source);
        }
        $this->setContents(file_get_contents($source));
    }
}