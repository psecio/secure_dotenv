<?php

namespace Psecio\SecureDotenv;

abstract class KeySource
{
    protected $content;

    public function getContent()
    {
        return $this->content;
    }
    public function setContent($content)
    {
        $this->content = $content;
    }
}