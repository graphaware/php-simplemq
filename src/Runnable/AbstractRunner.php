<?php

namespace GraphAware\SimpleMQ\Runnable;

use GraphAware\SimpleMQ\Exception\SimpleMQException;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Exception\AMQPProtocolException;
use PhpAmqpLib\Exception\AMQPRuntimeException;

abstract class AbstractRunner implements RunnableInterface
{
    /**
     * @var \PhpAmqpLib\Connection\AMQPConnection
     */
    protected $connection;

    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel
     */
    protected $channel;

    /**
     * @var array
     */
    protected $properties = [];

    /**
     *
     * Bootstraps the connection and the channel
     *
     * @throws \GraphAware\SimpleMQ\Exception\SimpleMQException
     */
    public function run()
    {
        $conn = $this->getExchange()->getConnection();
        try {
            $this->connection = new AMQPConnection($conn->getHost(), $conn->getPort(), $conn->getUser(), $conn->getPassword());
            $this->channel = $this->connection->channel();
        } catch (AMQPRuntimeException $e) {
            throw new SimpleMQException($e->getMessage());
        } catch (AMQPProtocolException $e) {
            throw new SimpleMQException($e->getMessage());
        }
        $this->channel->exchange_declare(
          $this->getExchange()->getName(),
          $this->getExchange()->getType(),
          false,
          $this->getExchange()->isIsDurable()
        );

        if ($this->getExchange()->isIsDurable()) {
            $this->properties['delivery_mode'] = 2;
        }
    }

    /**
     * Close the running connection
     */
    public function close()
    {
        if (null !== $this->connection && $this->connection->isConnected()) {
            $this->channel->close();
            $this->connection->close();
        }
    }

    /**
     * Returns whether or not the connection is running
     *
     * @return bool
     */
    public function isRunning()
    {
        if (null === $this->connection) {
            return false;
        }

        return $this->connection->isConnected();
    }

    /**
     * Returns the properties for the messages to be sent
     *
     * @return array|null
     */
    public function getProperties()
    {
        if (empty($this->properties)) {
            return null;
        }

        return $this->properties;
    }
}