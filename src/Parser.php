<?php

namespace Psecio\SecureDotenv;

use \M1\Env\Parser as M1Parser;

class Parser extends M1Parser
{
    /**
     * Path on disk to the .env configuration file
     *
     * @var string
     */
    protected $configPath;

    /**
     * The current instance of the Crypto class
     *
     * @var \Psecio\SecureDotenv\Crypto
     */
    protected $crypto;

    /**
     * Init the parser instance with the key provided (either a string or file path)
     * and the path to the configuration. If the config path isn't provided, it assumes
     * it is in the local directory.
     *
     * @param string $key Key value or file path to key
     * @param string $configPath Path to the .env configuration file
     */
    public function __construct($key, $configPath = null)
    {
        $this->setCrypto(new Crypto($key));

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

    /**
     * Set the current instance of the Crypto class
     *
     * @param \Psecio\SecureDotenv\Crypto $crypto
     */
    public function setCrypto(\Psecio\SecureDotenv\Crypto $crypto)
    {
        $this->crypto = $crypto;
    }

    /**
     * Set the path for the current configuration file
     *
     * @param string $configPath
     * @throws \InvalidArgumentException If the path is invalid
     */
    public function setConfigPath($configPath)
    {
        if (empty($configPath) || !is_file($configPath)) {
            throw new \InvalidArgumentException('Invalid config file path: '.$configPath);
        }
        $this->configPath = $configPath;
    }

    /**
     * Save a new encrypted value to the .env file
     *
     * @param string $keyName Key name (plain-text)
     * @param string $keyValue Value to set for the key (plain-text)
     * @param boolean $overwrite Flag to either overwrite the value that exists or leave it
     * @return boolean Success/fail of the write
     */
    public function save($keyName, $keyValue, $overwrite = false)
    {
        $ciphertext = $this->crypto->encrypt($keyValue);
        return $this->writeEnv($keyName, $ciphertext, $overwrite);
    }

    /**
     * Write the contents out to the .env configuration file
     *
     * @param string $keyName Key name (plain-text)
     * @param string $ciphertext Encrypted value
     * @param boolean $overwrite Flag to either overwrite the value that exists or leave it
     * @throws \Exception If the key name already exists and the overwrite flag isn't true
     * @return boolean Success/fail of file write
     */
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