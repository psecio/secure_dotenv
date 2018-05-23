<?php

namespace Psecio\SecureDotenv;

abstract class KeySource
{
    /**
     * Content of the key
     *
     * @var string
     */
    protected $content;

    /**
     * Get the current key contents
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the current key content
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
}