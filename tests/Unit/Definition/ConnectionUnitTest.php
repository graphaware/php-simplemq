<?php

namespace GraphAware\SimpleMQ\Tests\Unit\Definition;

use GraphAware\SimpleMQ\Definition\Connection;

class ConnectionUnitTest extends \PHPUnit_Framework_TestCase
{

    public function testConnectionConstruct()
    {
        $connection = new Connection('default', 'localhost', 5673, 'user', 'password');
        $this->assertEquals('default', $connection->getAlias());
        $this->assertEquals('localhost', $connection->getHost());
        $this->assertEquals(5673, $connection->getPort());
        $this->assertEquals('user', $connection->getUser());
        $this->assertEquals('password', $connection->getPassword());
    }

    public function testDefaultPortIsConfiguredWhenNotProvided()
    {
        $connection = new Connection('default', 'localhost', null, 'user', 'password');
        $this->assertEquals(5672, $connection->getPort());
    }
}