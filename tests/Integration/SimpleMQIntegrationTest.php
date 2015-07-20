<?php

namespace GraphAware\SimpleMQ\Tests\Integration;

use GraphAware\SimpleMQ\SimpleMQ;
use Symfony\Component\Yaml\Yaml;

class SimpleMQIntegrationTest extends \PHPUnit_Framework_TestCase
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

    public function testProducersCanRun()
    {
        $p1 = $this->simpleMQ->getProducer('producer-1');
        $p1->run();
        $this->assertTrue($p1->isRunning());

        $p2 = $this->simpleMQ->getProducer('producer-2');
        $p2->run();
        $this->assertTrue($p2->isRunning());
    }

    public function testMessageCanBeSentWhenConnectionIsNotYetRunning()
    {
        $p1 = $this->simpleMQ->getProducer('producer-1');
        $p1->close();
        $message = json_encode(array('id' => 1));
        $p1->sendMessage($message);
    }
}