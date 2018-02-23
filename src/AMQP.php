<?php

namespace duodai\amqp;

use duodai\amqp\base\QueueName;
use duodai\amqp\config\Config;
use duodai\amqp\objects\Channel;
use duodai\amqp\objects\Connection;
use duodai\amqp\objects\Message;
use duodai\amqp\objects\Output;
use duodai\amqp\objects\Queue;
use duodai\amqp\objects\Route;

/**
 * Class AMQP
 * Object oriented wrapper for php-amqp library
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class AMQP
{
    // TODO Review app structure, make it more flexible and less config-dependent
    // TODO rework component configuration, since component is now a composer package and it's folder is read-only
    // TODO replace webmozart/assert usage with own validation component
    // TODO add some means to declare queues on the fly
    // TODO test and correct PSR-0 namespaces
    // TODO add unit tests
    // TODO test installing via composer

    /**
     * @var Configuration
     */
    protected $config;
    /**
     * @var Connection[]
     */
    protected $connections = [];
    /**
     * @var Channel
     */
    protected $channel;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = new Config($config);
        foreach ($this->config->getConnectionConfigurations() as $connectionConfig) {
            $this->connections[] = $this->createConnection($connectionConfig);
        }
    }

    /**
     * @param array $config
     * @return Connection
     */
    protected function createConnection(array $config)
    {
        return new Connection($config);
    }

    /**
     * Get message from queue
     * @param QueueName $queueName
     * @param bool|false $autoAck
     * @return Output|null
     */
    public function pull(QueueName $queueName, $autoAck = false)
    {
        Assert::boolean($autoAck, __METHOD__ . ' error: $autoAck must be boolean, got ' . gettype($autoAck));
        $this->ensureIsConnected();
        $queue = $this->buildQueueObject($queueName, $this->channel);
        return $queue->pull($autoAck);
    }

    /**
     * Reconnect if not connected
     */
    protected function ensureIsConnected()
    {
        if (!isset($this->channel) || !$this->channel->isConnected()) {
            $this->connect();
        }
    }

    /**
     * @throws AmqpException
     */
    protected function connect()
    {
        $connection = $this->getAccessibleConnection();
        $this->channel = $this->createChannel($connection);
    }

    /**
     * @return Connection
     * @throws AmqpException
     */
    protected function getAccessibleConnection()
    {
        for ($i = 0; $i < $this->config->getMaxTriesToConnect(); $i++) {
            $connections = $this->connections;
            shuffle($connections);
            while (count($connections) > 0) {
                $connection = array_shift($connections);
                if ($connection->connect()) {
                    return $connection;
                }
            }
        }
        throw new AmqpException(__METHOD__ . ' error: Connect failed: no servers available');
    }

    /**
     * @param Connection $connection
     * @return Channel
     */
    protected function createChannel(Connection $connection)
    {
        return new Channel($connection);
    }

    /**
     * @param QueueName $name
     * @param Channel $channel
     * @return Queue
     */
    protected function buildQueueObject(QueueName $name, Channel $channel)
    {
        return $this->getQueueBuilder()->create($name, $channel);
    }

    /**
     * @return QueueObjectBuilder
     */
    protected function getQueueBuilder()
    {
        return new QueueObjectBuilder();
    }

    /**
     * Put message into queue
     * @param Message $message
     * @return bool
     */
    public function push(Message $message)
    {
        $this->ensureIsConnected();
        $routeName = $message->getRoute();
        $route = $this->buildRouteObject($routeName, $this->channel);
        return $route->getExchange()->push($message);
    }

    /**
     * @param RouteName $routeName
     * @param Channel $channel
     * @return Route
     */
    protected function buildRouteObject(RouteName $routeName, Channel $channel)
    {
        return $this->getRouteBuilder()->create($routeName, $channel);
    }

    /**
     * @return RouteObjectBuilder
     */
    protected function getRouteBuilder()
    {
        return new RouteObjectBuilder();
    }

    /**
     * Confirm message removal
     * @param Output $output
     * @return bool
     */
    public function ack(Output $output)
    {
        $this->ensureIsConnected();
        return $output->ack();
    }

    /**
     * Return message to queue
     * @param Output $output
     * @return bool
     */
    public function nack(Output $output)
    {
        $this->ensureIsConnected();
        return $output->nack();
    }

    /**
     * Generate new Message object
     * @param string $data
     * @param RouteName $route
     * @return Message
     */
    public function createMessage($data, RouteName $route)
    {
        return new Message($data, $route);
    }
}
