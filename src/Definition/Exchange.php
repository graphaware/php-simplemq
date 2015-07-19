<?php

namespace GraphAware\SimpleMQ\Definition;

class Exchange
{
    /**
     *
     */
    const EXCHANGE_TYPE_FANOUT = 'fanout';

    /**
     *
     */
    const EXCHANGE_TYPE_DIRECT = 'direct';

    /**
     *
     */
    const EXCHANGE_TYPE_TOPIC = 'topic';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \GraphAware\SimpleMQ\Definition\Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $isDurable;

    /**
     * @param string $name
     * @param string $type
     * @param bool $durable
     * @param \GraphAware\SimpleMQ\Definition\Connection $connection
     */
    public function __construct($name, $type, $durable = false, Connection $connection)
    {
        $this->name = (string) $name;
        $this->type = $type;
        $this->connection = $connection;
        $this->isDurable = (bool) $durable;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return \GraphAware\SimpleMQ\Definition\Connection
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return boolean
     */
    public function isIsDurable() {
        return $this->isDurable;
    }


}