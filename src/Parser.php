<?php

namespace Psecio\SecureDotenv;

class Parser
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
    }

    /**
     * Decrypt the values provided
     * Supports sections
     *
     * @param array $values
     * @return array Decrypted values
     */
    public function decryptValues(array $values) : array
    {
        foreach ($values as $index => $value) {
            if (is_array($value)) {
                foreach ($value as $i => $v) {
                    $de = $this->crypto->decrypt(trim($v));
                    $values[$index][$i] = ($de == null) ? $v : $de;
                }
            } else {
                $de = $this->crypto->decrypt(trim($value));
                $values[$index] = ($de == null) ? $value : $de;
            }
        }
        return $values;
    }

    /**
     * Read in the configuration file
     *
     * @param string $configPath Configuration file path
     * @return string
     */
    public function loadFile($configPath)
    {
        $contents = $this->decryptValues(File::read($configPath));
        return $contents;
    }

    /**
     * Set the current instance of the Crypto class
     *
     * @param \Psecio\SecureDotenv\Crypto $crypto
     */
    public function setCrypto(Crypto $crypto)
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
        return $this->writeEnv($keyName, $keyValue, $overwrite);
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
    public function writeEnv($keyName, $keyValue, $overwrite = false)
    {
        $contents = $this->loadFile($this->configPath);

        // read from the .env file, update any that need it or add a new one
        if (isset($contents[$keyName]) && $overwrite == false) {
            throw new \Exception('Key name "'.$keyName.'" already exists!');
        }

        // If it's not already set (or overwrite is true), write it out
        $contents[$keyName] = $keyValue;

        foreach ($contents as $index => $value) {
            if (is_array($value)) {
                foreach ($value as $i => $v) {
                    $contents[$index][$i] = $this->crypto->encrypt($v);
                }
            } else {
                $contents[$index] = $this->crypto->encrypt($value);
            }
        }

        return File::write($contents, $this->configPath);
    }

    /**
     * Get the contents of the current configuration file
     *
     * @param string $keyName Name of key to locate [optional]
     * @return array|string
     */
    public function getContent($keyName = null)
    {
        $contents = $this->loadFile($this->configPath);

        if ($keyName !== null && isset($contents[$keyName])) {
            return $contents[$keyName];
        }
        return $contents;
    }
}
