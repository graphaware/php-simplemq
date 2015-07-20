<?php

namespace GraphAware\SimpleMQ\Tests\Unit;

use GraphAware\SimpleMQ\SimpleMQ;

class SimpleMQUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testNewObjectIsReturnedBuilt()
    {
        $smq = SimpleMQ::withYamlConfigFile(__DIR__.'/../_assets/config.yml');

        $this->assertInstanceOf('GraphAware\SimpleMQ\SimpleMQ', $smq);
        $this->assertEquals('producer-1', $smq->getProducer('producer-1')->getName());
    }
}