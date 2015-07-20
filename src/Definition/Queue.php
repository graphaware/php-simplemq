<?php

namespace GraphAware\SimpleMQ\Definition;

class Queue
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $durable;

    /**
     * @var int
     */
    protected $prefetchCount;

    /**
     * @var bool
     */
    protected $autoDelete;

    /**
     * var bool
     */
    protected $exclusive;

    /**
     * @param $name
     * @param bool $durable
     * @param bool $autoDelete
     * @param bool $exclusive
     */
    public function __construct($name, $durable = false, $autoDelete = false, $exclusive = false)
    {
        $this->name = (string) $name;
        $this->durable = (bool) $durable;
        $this->autoDelete = (bool) $autoDelete;
        $this->exclusive = (bool) $exclusive;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isDurable()
    {
        return $this->durable;
    }

    /**
     * @return bool
     */
    public function isAutoDelete()
    {
        return $this->autoDelete;
    }

    /**
     * @return bool
     */
    public function isExclusive()
    {
        return $this->exclusive;
    }
}