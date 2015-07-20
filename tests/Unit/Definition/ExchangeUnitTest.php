<?php

namespace GraphAware\SimpleMQ\Tests\Unit\Definition;

use GraphAware\SimpleMQ\Definition\Connection;
use GraphAware\SimpleMQ\Definition\Exchange;

class ExchangeUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruction()
    {
        $connection = new Connection('default', 'localhost', null, 'user', 'password');
        $exchange = new Exchange('ex-1', 'fanout', true, $connection);

        $this->assertEquals($connection, $exchange->getConnection());
        $this->assertEquals('ex-1', $exchange->getName());
        $this->assertTrue($exchange->isIsDurable());
        $this->assertEquals('fanout', $exchange->getType());
    }

    public function testDefaultExchangesAreNotDurable()
    {
        $connection = new Connection('default', 'localhost', null, 'user', 'password');
        $exchange = new Exchange('ex-1', 'fanout', null, $connection);

        $this->assertFalse($exchange->isIsDurable());
    }
}