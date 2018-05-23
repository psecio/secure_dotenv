<?php

namespace Psecio\SecureDotenv\KeySource;

class KeyFile extends \Psecio\SecureDotenv\KeySource
{
    public function __construct($source)
    {
        if (!is_file($source)) {
            throw new \InvalidArgumentException('Invalid source: '.$source);
        }
        echo 'source: '.$source;
        $this->setContent(file_get_contents($source));
    }
}