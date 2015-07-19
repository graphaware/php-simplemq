<?php

namespace GraphAware\SimpleMQ\Runnable;

use GraphAware\SimpleMQ\Definition\Exchange;

interface RunnableInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return \GraphAware\SimpleMQ\Definition\Exchange
     */
    public function getExchange();

    /**
     * @return null|string
     */
    public function getRoutingKey();

    /**
     * @return boolean
     */
    public function isAutoClose();

    /**
     * @return void
     */
    public function run();
}