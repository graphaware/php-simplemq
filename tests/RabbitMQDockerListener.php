<?php

class RabbitMQDockerListener extends  PHPUnit_Framework_BaseTestListener
{
    protected $isDockerImageStarted = false;

    protected $isCI = false;

    public function __construct()
    {
        if (getenv('TRAVIS') !== false) {
            $this->isCI = true;
        } else {
            $this->boot();
        }
    }

    public function __destruct()
    {
        $this->shut();
        exit();
    }

    private function boot()
    {
        if ($this->isCI()) { return; }
        if (!$this->isDockerImageStarted) {
            echo 'SimpleMQ Test Suite Started, Starting RabbitMQ docker image' . PHP_EOL;
            shell_exec(__DIR__.'/_scripts/setup-rabbit-docker.sh');
            sleep(5);
            $this->isDockerImageStarter = true;
        }
    }

    private function shut()
    {
        if ($this->isCI()) { return; }
        echo 'Stopping the Docker Rabbit MQ daemon ... ';
        shell_exec(__DIR__.'/_scripts/teardown-rabbit-docker.sh');
        echo ' done ' . PHP_EOL;
    }

    private function isCI()
    {
        return $this->isCI;
    }
}