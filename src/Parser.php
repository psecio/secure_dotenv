<?php

namespace Psecio\SecureDotenv;

use \M1\Env\Parser as M1Parser;

class Parser extends M1Parser
{
    protected $keyPath;
    protected $configPath;
    protected $crypto;

    public function __construct($keyPath, $configPath = null)
    {
        $this->setCrypto(new Crypto($keyPath));

        if ($configPath == null) {
            $configPath = __DIR__.'/.env';
        }
        $this->setConfigPath($configPath);

        // Parse the contents normally, then...
        $content = file_get_contents($configPath);
        parent::__construct($content);

        // Reparse with our special secure value parser
        $this->value_parser = new SecureValueParser($this, $this->crypto);
        $this->doParse($content);
    }

    public function setCrypto(\Psecio\SecureDotenv\Crypto $crypto)
    {
        $this->crypto = $crypto;
    }

    public function setConfigPath($configPath)
    {
        if (empty($configPath) || !is_file($configPath)) {
            throw new \InvalidArgumentException('Invalid config file path: '.$configPath);
        }
        $this->configPath = $configPath;
    }

    public function save($keyName, $keyValue, $overwrite = false)
    {
        $ciphertext = $this->crypto->encrypt($keyValue);
        return $this->writeEnv($keyName, $ciphertext, $overwrite);
    }

    public function writeEnv($keyName, $ciphertext, $overwrite = false)
    {
        // read from the .env file, update any that need it or add a new one
        $options = M1Parser::parse(file_get_contents($this->configPath));

        if (isset($options[$keyName]) && $overwrite == false) {
            throw new \Exception('Key name "'.$keyName.'" already exists!');
        }

        // If it's not already set (or overwrite is true), write it out
        $options[$keyName] = $ciphertext;
        $lines = [];
        foreach ($options as $keyName => $keyValue) {
            $lines[] = $keyName.'='.$keyValue;
        }
        return file_put_contents($this->configPath, implode("\n", $lines));
    }
}