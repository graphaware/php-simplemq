<?php

namespace GraphAware\SimpleMQ\Definition;

use GraphAware\SimpleMQ\Definition\Exchange;
use GraphAware\SimpleMQ\Definition\Queue;
use GraphAware\SimpleMQ\Runnable\AbstractRunner;

class Consumer extends AbstractRunner
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var \GraphAware\SimpleMQ\Definition\Exchange
     */
    protected $exchange;

    /**
     * @var string
     */
    protected $routingKey;

    /**
     * @var bool
     */
    protected $isAutoClose;

    /**
     * @var \GraphAware\SimpleMQ\Definition\Queue
     */
    protected $queue;

    /**
     * @var array
     */
    protected $bindings = [];

    /**
     * @var boolean
     */
    protected $messageAcknowledgment;


    /**
     * @param $name
     * @param \GraphAware\SimpleMQ\Definition\Exchange $exchange
     * @param \GraphAware\SimpleMQ\Definition\Queue $queue
     * @param string|null $routingKey
     * @param bool $autoClose Whether or not the connection should be closed after a message has been received, default to false
     * @param bool $messageAck Whether or not the consumer should perform message acknowledgment
     */
    public function __construct($name, Exchange $exchange, Queue $queue, $routingKey = null, $autoClose = false, $messageAck = false)
    {
        $this->name = (string) $name;
        $this->exchange = $exchange;
        $this->queue = $queue;
        $this->routingKey = null !== $routingKey ? (string) $routingKey : '';
        $this->isAutoClose = $autoClose;
        $this->messageAcknowledgment = $messageAck;
    }


    /**
     * Consumes the queue
     *
     * @param $callback
     */
    public function consume($callback)
    {
        if (!$this->isRunning()) {
            $this->run();
        }

        $this->channel->basic_consume($this->queue->getName(), '', false, $this->shouldAcknowledgeMessages(), false, false, $callback);
        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }

    /**
     * Bootstrap the connection
     *
     * @throws \GraphAware\SimpleMQ\Exception\SimpleMQException
     */
    public function run()
    {
        parent::run();
        $this->channel->queue_declare($this->queue->getName(), false, $this->queue->isDurable());
        foreach ($this->bindings as $binding) {
            $this->channel->queue_bind($this->queue->getName(), $this->exchange->getName(), $binding['routing_key']);
        }
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return \GraphAware\SimpleMQ\Definition\Exchange
     */
    public function getExchange() {
        return $this->exchange;
    }

    /**
     * @return string
     */
    public function getRoutingKey() {
        return $this->routingKey;
    }

    /**
     * @return bool
     */
    public function isAutoClose() {
        return $this->isAutoClose;
    }

    /**
     * @return Queue
     */
    public function getQueue() {
        return $this->queue;
    }

    /**
     * @return bool
     */
    public function shouldAcknowledgeMessages()
    {
        return $this->messageAcknowledgment;
    }

    /**
     * @param string $queue
     * @param string $routingKey
     */
    public function addBinding($queue, $routingKey = '')
    {
        $this->bindings[] = [
            'queue' => $queue,
            'routing_key' => $routingKey
        ];
    }
}