<?php

namespace GraphAware\SimpleMQ\Definition;

class Connection
{
    /**
     * @var string
     */
    protected $alias;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var int
     */
    protected $port;

    /**
     * @param $alias
     * @param $host
     * @param int $port
     * @param $user
     * @param $password
     */
    public function __construct($alias, $host, $port = null, $user, $password)
    {
        $this->alias = $alias;
        $this->host = (string) $host;
        $this->port = null !== $port ? (int) $port : 5672;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getAlias() {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * @return mixed
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @return int
     */
    public function getPort() {
        return $this->port;
    }




}