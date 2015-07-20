<?php

namespace GraphAware\SimpleMQ\Definition;

use GraphAware\SimpleMQ\Definition\Exchange;
use GraphAware\SimpleMQ\Definition\Queue;
use GraphAware\SimpleMQ\Runnable\AbstractRunner;
use PhpAmqpLib\Message\AMQPMessage;

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
     * @var bool
     */
    protected $messageAcknowledgment;

    /**
     * @var bool
     */
    protected $autoAcknowledge;


    /**
     * @param $name
     * @param \GraphAware\SimpleMQ\Definition\Exchange $exchange
     * @param \GraphAware\SimpleMQ\Definition\Queue $queue
     * @param string|null $routingKey
     * @param bool $autoClose Whether or not the connection should be closed after a message has been received, default to false
     * @param bool $messageAck Whether or not the consumer should perform message acknowledgment
     * @param bool $autoAcknowledge Whether or not the consumer should automatically acknowledge messages on reception
     */
    public function __construct($name, Exchange $exchange, Queue $queue, $routingKey = null, $autoClose = false, $messageAck = false, $autoAcknowledge = false)
    {
        $this->name = (string) $name;
        $this->exchange = $exchange;
        $this->queue = $queue;
        $this->routingKey = null !== $routingKey ? (string) $routingKey : '';
        $this->isAutoClose = $autoClose;
        $this->messageAcknowledgment = $messageAck;
        $this->autoAcknowledge = $autoAcknowledge;
    }

    /**
     * Returns a single message body or null when no message present in the queue
     *
     * @return \PhpAmqpLib\Message\AMQPMessage
     */
    public function getMessage()
    {
        if (!$this->isRunning()) {
            $this->run();
        }

        $message = $this->channel->basic_get($this->queue->getName(), !$this->shouldAcknowledgeMessages());
        if (null !== $message) {
            return $message;
        }

        return null;
    }

    /**
     * Receive multiple messages from Queue
     *
     * @param int $batch Number of messages desired
     * @param int $maxAttempts Number of attempts to reach the desired batch before stopping reading the queue
     * @return \PhpAmqpLib\Message\AMQPMessage[]
     */
    public function getMessageBatch($batch = 1, $maxAttempts = null)
    {
        $messages = [];
        $i = 1;
        $loop = null !== $maxAttempts && $maxAttempts > $batch ? $maxAttempts : $batch;
        while ($i <= $loop && count($messages) < $batch) {
            $msg = $this->getMessage();
            if (null !== $msg) {
                $messages[] = $msg;
            }
            $i++;
        }

        return $messages;
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

        $this->channel->basic_consume($this->queue->getName(), '', false, !$this->shouldAcknowledgeMessages(), false, false, $callback);
        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }

    /**
     * Purges the queue bound to this consumer
     */
    public function purge()
    {
        if (!$this->isRunning()) {
            $this->run();
        }

        $this->channel->queue_purge($this->queue->getName());
    }

    /**
     * Bootstrap the connection
     *
     * @return void
     * @throws \GraphAware\SimpleMQ\Exception\SimpleMQException
     */
    public function run()
    {
        parent::run();
        $this->channel->queue_declare($this->queue->getName(), false, $this->queue->isDurable(), $this->queue->isExclusive(), $this->queue->isAutoDelete());
        foreach ($this->bindings as $binding) {
            $this->channel->queue_bind($this->queue->getName(), $this->exchange->getName(), $binding['routing_key']);
        }
    }

    public function close()
    {
        if ($this->isRunning() && $this->queue->isAutoDelete()) {
            $this->channel->queue_purge($this->queue->getName(), true);
        }

        parent::close();
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

    /**
     * Returns Exchange and Queue bindings
     *
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }
}