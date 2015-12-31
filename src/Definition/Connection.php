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
     * @var string
     */
    protected $vhost;

    /**
     * @param $alias
     * @param $host
     * @param int $port
     * @param $user
     * @param $password
     */
    public function __construct($alias, $host, $port = null, $user, $password, $vhost)
    {
        $this->alias = $alias;
        $this->host = (string) $host;
        $this->port = null !== $port ? (int) $port : 5672;
        $this->user = $user;
        $this->password = $password;
        $this->vhost = $vhost;
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

    /**
     * @return string
     */
    public function getVhost()
    {
        return $this->vhost;
    }

}