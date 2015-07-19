<?php

namespace GraphAware\SimpleMQ;

use GraphAware\SimpleMQ\Configuration\ConfigurationReader;

class SimpleMQ
{
    /**
     * @var \GraphAware\SimpleMQ\MQManager
     */
    protected $manager;

    /**
     * @param string $fileLocation
     * @return \GraphAware\SimpleMQ\SimpleMQ
     * @throws \GraphAware\SimpleMQ\Exception\SimpleMQException
     */
    public static function withYamlConfigFile($fileLocation)
    {
        $reader = new ConfigurationReader($fileLocation);

        return new self($reader->getConfig());
    }

    /**
     * @param array $config
     * @return \GraphAware\SimpleMQ\SimpleMQ
     */
    public static function withArrayConfig(array $config)
    {
        return new self($config);
    }

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->manager = new MQManager($config);
    }

    /**
     * @param $name
     * @return \GraphAware\SimpleMQ\Definition\Producer
     */
    public function getProducer($name)
    {
        return $this->manager->getProducer($name);
    }

    /**
     * @param $name
     * @return \GraphAware\SimpleMQ\Definition\Consumer
     */
    public function getConsumer($name)
    {
        return $this->manager->getConsumer($name);
    }
}