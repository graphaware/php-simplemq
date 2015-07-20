<?php

namespace GraphAware\SimpleMQ\Tests\Unit\Definition;

use GraphAware\SimpleMQ\Definition\Queue;

class QueueUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruction()
    {
        $queue = new Queue('my-queue', true);
        $this->assertEquals('my-queue', $queue->getName());
        $this->assertTrue($queue->isDurable());
        $this->assertFalse($queue->isAutoDelete());
    }

    public function testDefaultQueuesAreNotDurable()
    {
        $queue = new Queue('my-queue');
        $this->assertFalse($queue->isDurable());
    }

    public function testQueueIsMarkedAsAutoDelete()
    {
        $queue = new Queue('my-queue', null, true);
        $this->assertTrue($queue->isAutoDelete());
        $this->assertFalse($queue->isDurable());
    }
}