<?php

namespace GraphAware\SimpleMQ\Tests\Integration;

use GraphAware\SimpleMQ\SimpleMQ;
use Symfony\Component\Yaml\Yaml;

class ConsumerIntegrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GraphAware\SimpleMQ\SimpleMQ;
     */
    protected $simpleMQ;

    public function setUp()
    {
        $travisConfig = [
          'host' => 'localhost',
          'port' => 5672,
          'user' => 'guest',
          'password' => 'guest'
        ];
        $configFile = __DIR__.'/../_assets/config.yml';
        $config = Yaml::parse(file_get_contents($configFile));
        if (getenv('TRAVIS') !== false) {
            $config['simple_mq']['connections']['default'] = $travisConfig;
        }

        $this->simpleMQ = SimpleMQ::withArrayConfig($config);
    }

    public function testGetMessageIsStartingConnectionWhenNotRunning()
    {
        $c1 = $this->simpleMQ->getConsumer('consumer-1');
        $c1->close();
        $this->assertFalse($c1->isRunning());
        $c1->getMessage();
        $this->assertTrue($c1->isRunning());
    }

    public function testQueuesAreDeletedAndPurgedWhenAutoDeleteIsTrue()
    {
        $p2 = $this->simpleMQ->getProducer('producer-2');
        $c2 = $this->simpleMQ->getConsumer('consumer-2');
        $this->assertTrue($c2->getQueue()->isExclusive());
        $this->assertNull($c2->getMessage());
        for ($i = 1; $i < 10; $i++) {
            $msg = json_encode(array('id' => $i));
            $p2->sendMessage($msg);
        }
        $c2->close();
        $this->assertNull($c2->getMessage());

        // Close the connection as the queue is exclusive and may throw resource locked exceptions in other tests
        $c2->close();
    }

    public function testConsumerCanReceiveMoreThanOneMessage()
    {
        $p2 = $this->simpleMQ->getProducer('producer-2');
        $c2 = $this->simpleMQ->getConsumer('consumer-2');
        if ($c2->isRunning()) {
            $c2->close();
        }
        $c2->purge();
        $this->assertNull($c2->getMessage());
        $this->assertTrue($c2->isRunning());
        for ($i = 1; $i <= 10; $i++) {
            $msg = json_encode(array('id' => $i));
            $p2->sendMessage($msg);
        }

        $this->assertCount(10, $c2->getMessageBatch(10, 20));
    }
}