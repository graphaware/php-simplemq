<?php

namespace GraphAware\SimpleMQ\Definition;

use GraphAware\SimpleMQ\Runnable\AbstractRunner;
use PhpAmqpLib\Message\AMQPMessage;

class Producer extends AbstractRunner
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var Exchange
     */
    protected $exchange;

    /**
     * @var bool
     */
    protected $autoClose;

    /**
     * @var string
     */
    protected $routingKey;

    /**
     * @param $name
     * @param \GraphAware\SimpleMQ\Definition\Exchange $exchange
     * @param null $routingKey
     * @param bool $autoClose
     */
    public function __construct($name, Exchange $exchange, $routingKey = null, $autoClose = false)
    {
        $this->name = (string) $name;
        $this->exchange = $exchange;
        $this->autoClose = (bool) $autoClose;
        $this->routingKey = null !== $routingKey ? (string) $routingKey : '';
    }

    /**
     * Sends a message to an exchange
     *
     * @param mixed $message
     * @throws \GraphAware\SimpleMQ\Exception\SimpleMQException
     */
    public function sendMessage($message)
    {
        if (!$this->isRunning()) {
            $this->run();
        }

        $message = new AMQPMessage($message, $this->getProperties());
        $this->channel->basic_publish($message, $this->getExchange()->getName(), $this->getRoutingKey());

        if ($this->isAutoClose()) {
            $this->close();
        }
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return Exchange
     */
    public function getExchange() {
        return $this->exchange;
    }

    /**
     * @return bool
     */
    public function isAutoClose() {
        return $this->autoClose;
    }

    /**
     * @return string
     */
    public function getRoutingKey() {
        return $this->routingKey;
    }
}