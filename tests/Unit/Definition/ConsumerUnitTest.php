<?php

namespace GraphAware\SimpleMQ\Tests\Unit\Definition;

use GraphAware\SimpleMQ\Definition\Queue,
    GraphAware\SimpleMQ\Definition\Connection,
    GraphAware\SimpleMQ\Definition\Exchange,
    GraphAware\SimpleMQ\Definition\Consumer;

class ConsumerUnitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GraphAware\SimpleMQ\Definition\Consumer
     */
    protected $consumer;

    protected $exchange;

    protected $queue;

    public function setUp()
    {
        $connection = new Connection('default', 'localhost', null, 'user', 'password');
        $exchange = new Exchange('ex-1', 'fanout', true, $connection);
        $queue = new Queue('queue1', true);
        $this->consumer = new Consumer('consumer-1', $exchange, $queue);
        $this->exchange = $exchange;
        $this->queue = $queue;
    }

    public function testConstruction()
    {
        $this->assertEquals('consumer-1', $this->consumer->getName());
        $this->assertEquals('', $this->consumer->getRoutingKey());
        $this->assertFalse($this->consumer->shouldAcknowledgeMessages());
        $this->assertFalse($this->consumer->isAutoClose());
        $this->assertFalse($this->consumer->isRunning());
    }

    public function testExchange()
    {
        $this->assertEquals($this->exchange, $this->consumer->getExchange());
    }

    public function testQueue()
    {
        $this->assertEquals($this->queue, $this->consumer->getQueue());
    }

    /**
     * @expectedException \GraphAware\SimpleMQ\Exception\SimpleMQException
     */
    public function testRunWillThrowExceptionIfConnectionIsWrong()
    {
        $this->consumer->run();
    }
}