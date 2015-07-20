<?php

namespace GraphAware\SimpleMQ\Tests\Unit;

use GraphAware\SimpleMQ\MQManager;
use Symfony\Component\Yaml\Yaml;

class MQManagerUnitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GraphAware\SimpleMQ\MQManager
     */
    protected $manager;

    public function setUp()
    {
        $this->manager = new MQManager(Yaml::parse(file_get_contents(__DIR__.'/../_assets/config.yml')));
    }

    public function testConnectionsAreSet()
    {
        $this->assertEquals('default', $this->manager->getConnection('default')->getAlias());
    }

    public function testExchangesAreSet()
    {
        $this->assertExchangeExist('exchange-1');
        $this->assertExchangeExist('exchange-3');
        $this->assertExchangeExist('exchange-2');
    }

    public function testProducersAreSet()
    {
        $this->assertProducerExist('producer-1');
        $this->assertProducerExist('producer-2');
        $this->assertProducerExist('producer-3');
    }

    public function testConsumersAreSet()
    {
        $this->assertConsumerExist('consumer-1');
        $this->assertConsumerExist('consumer-2');
        $this->assertConsumerExist('consumer-3');
    }

    /**
     * @expectedException \GraphAware\SimpleMQ\Exception\SimpleMQException
     */
    public function testExceptionIsThrownWhenItemDoesNotExist()
    {
        $this->manager->getConnection('hello');
    }

    private function assertExchangeExist($exchange)
    {
        return $this->assertInstanceOf('\GraphAware\SimpleMQ\Definition\Exchange', $this->manager->getExchange($exchange));
    }

    private function assertProducerExist($producer)
    {
        return $this->assertInstanceOf('GraphAware\SimpleMQ\Definition\Producer', $this->manager->getProducer($producer));
    }

    private function assertConsumerExist($consumer)
    {
        return $this->assertInstanceOf('GraphAware\SimpleMQ\Definition\Consumer', $this->manager->getConsumer($consumer));
    }

}