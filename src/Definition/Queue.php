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
     * @param $name
     * @param bool $durable
     */
    public function __construct($name, $durable = false)
    {
        $this->name = (string) $name;
        $this->durable = (bool) $durable;
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
}