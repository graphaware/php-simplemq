<?php

namespace GraphAware\SimpleMQ\Configuration;

use GraphAware\SimpleMQ\Exception\SimpleMQException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ConfigurationReader
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fs;

    /**
     * @var string
     */
    protected $fileLocation;

    /**
     * @var array
     */
    protected $config;

    /**
     * @param $fileLocation
     * @throws \GraphAware\SimpleMQ\Exception\SimpleMQException
     */
    public function __construct($fileLocation)
    {
        $this->fs = new Filesystem();
        $this->fileLocation = $fileLocation;
        $this->read();
    }

    /**
     * @return mixed
     * @throws \GraphAware\SimpleMQ\Exception\SimpleMQException
     */
    public function getConfig()
    {
        if (null === $this->config) {
            throw new SimpleMQException('The configuration has not yet been processed');
        }

        return $this->config;
    }

    /**
     * @throws \GraphAware\SimpleMQ\Exception\SimpleMQException
     */
    private function read()
    {
        if (!$this->fs->exists($this->fileLocation)) {
            throw new SimpleMQException(sprintf('The configuration file "%s" could not be found', $this->fileLocation));
        }

        $content = file_get_contents($this->fileLocation);
        try {
            $this->config = Yaml::parse($content);
        } catch (ParseException $e) {
            throw new SimpleMQException($e->getMessage());
        }
    }
}