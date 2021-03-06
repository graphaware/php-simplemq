<?php

namespace GraphAware\SimpleMQ;

use GraphAware\SimpleMQ\Definition\Connection;
use GraphAware\SimpleMQ\Definition\Consumer;
use GraphAware\SimpleMQ\Definition\Exchange;
use GraphAware\SimpleMQ\Definition\Producer;
use GraphAware\SimpleMQ\Definition\Queue;
use GraphAware\SimpleMQ\Exception\SimpleMQException;

class MQManager
{
    /**
     * @var \GraphAware\SimpleMQ\Definition\Connection[]
     */
    protected $connections = [];

    /**
     * @var \GraphAware\SimpleMQ\Definition\Exchange[]
     */
    protected $exchanges = [];

    /**
     * @var \GraphAware\SimpleMQ\Definition\Producer[]
     */
    protected $producers = [];

    /**
     * @var \GraphAware\SimpleMQ\Definition\Consumer[]
     */
    protected $consumers = [];

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->buildConfig($config);
    }

    /**
     * @return \GraphAware\SimpleMQ\Definition\Producer[]
     */
    public function getProducers()
    {
        return $this->producers;
    }

    /**
     * @return \GraphAware\SimpleMQ\Definition\Consumer[]
     */
    public function getConsumers()
    {
        return $this->consumers;
    }

    /**
     * @return \GraphAware\SimpleMQ\Definition\Exchange[]
     */
    public function getExchanges()
    {
        return $this->exchanges;
    }

    /**
     * @param string $key
     * @return \GraphAware\SimpleMQ\Definition\Producer
     * @throws \GraphAware\SimpleMQ\Exception\SimpleMQException When the producer doesn't exist
     */
    public function getProducer($key)
    {
        return $this->get($key, 'producers');
    }

    /**
     * @param string $key
     * @return \GraphAware\SimpleMQ\Definition\Consumer
     * @throws \GraphAware\SimpleMQ\Exception\SimpleMQException When the consumer doesn't exist
     */
    public function getConsumer($key)
    {
        return $this->get($key, 'consumers');
    }

    /**
     * @param string $key
     * @return \GraphAware\SimpleMQ\Definition\Connection
     * @throws \GraphAware\SimpleMQ\Exception\SimpleMQException When the connection doesn't exist
     */
    public function getConnection($key)
    {
        return $this->get($key, 'connections');
    }

    /**
     * @param string $key
     * @return \GraphAware\SimpleMQ\Definition\Exchange
     * @throws \GraphAware\SimpleMQ\Exception\SimpleMQException When the exchange doesn't exist
     */
    public function getExchange($key)
    {
        return $this->get($key, 'exchanges');
    }

    /**
     * @param string $key
     * @param string $type
     * @return mixed
     * @throws \GraphAware\SimpleMQ\Exception\SimpleMQException When the key for type doesn't exist
     */
    public function get($key, $type)
    {
        if (!array_key_exists($key, $this->$type)) {
            throw new SimpleMQException(sprintf('The %s "%s" is not defined', $type, $key));
        }
        $coll = $this->$type;

        return $coll[$key];
    }

    private function buildConfig(array $config)
    {
        if (!isset($config['simple_mq'])) {
            throw new SimpleMQException('The configuration key "simple_mq" is missing from the configuration definition');
        }
        $definitions = $config['simple_mq'];
        foreach ($definitions['connections'] as $key => $definition) {
            $this->connections[$key] = new Connection($key, $definition['host'], $definition['port'], $definition['user'], $definition['password'], $definition['vhost']);
        }

        foreach ($definitions['exchanges'] as $k => $ex) {
            $durable = isset($ex['durable']) ? $ex['durable'] : false;
            $exchange = new Exchange($k, $ex['type'], $durable, $this->getConnection($ex['connection']));
            $this->exchanges[$k] = $exchange;
        }

        foreach ($definitions['producers'] as $k => $pr) {
            $rk = isset($pr['routing_key']) ? $pr['routing_key'] : null;
            $autoClose = isset($pr['auto_close']) ? $pr['auto_close'] : false;
            $this->producers[$k] = new Producer($k, $this->getExchange($pr['exchange']), $rk, $autoClose);
        }

        foreach ($definitions['consumers'] as $k => $co) {
            $rk = isset($co['routing_key']) ? $co['routing_key'] : null;
            $autoClose = isset($co['auto_close']) ? $co['auto_close'] : false;
            $queueAutoDelete = isset($co['queue']['auto_delete']) ? $co['queue']['auto_delete'] : false;
            $queueExclusive = isset($co['queue']['exclusive']) ? $co['queue']['exclusive'] : false;
            $queue = new Queue($co['queue']['name'], $co['queue']['durable'], $queueAutoDelete, $queueExclusive);
            $ack = isset($co['ack']) ? $co['ack'] : false;
            $consumer = new Consumer($k, $this->getExchange($co['exchange']), $queue, $rk, $autoClose, $ack);
            if (isset($co['queue']['bindings'])) {
                foreach ($co['queue']['bindings'] as $binding) {
                    $rk = isset($binding['routing_key']) ? $binding['routing_key'] : null;
                    $consumer->addBinding($binding['queue'], $rk);
                }
            }
            $this->consumers[$k] = $consumer;
        }
    }
}