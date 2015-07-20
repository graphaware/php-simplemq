<?php

namespace GraphAware\SimpleMQ\Tests\Unit\Definition;

use GraphAware\SimpleMQ\Definition\Producer,
    GraphAware\SimpleMQ\Definition\Exchange,
    GraphAware\SimpleMQ\Definition\Connection;

class ProducerUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruction()
    {
        $connection = new Connection('default', 'localhost', null, 'user', 'password');
        $exchange = new Exchange('ex-1', 'fanout', true, $connection);
        $producer = new Producer('producer-1', $exchange);

        $this->assertEquals($producer->getExchange()->getConnection(), $connection);
        $this->assertEquals($producer->getExchange(), $exchange);
        $this->assertEquals('producer-1', $producer->getName());
        $this->assertFalse($producer->isAutoClose());
        $this->assertFalse($producer->isRunning());
        $this->assertEquals('', $producer->getRoutingKey());
        $this->assertEquals('fanout', $producer->getExchange()->getType());
    }
}