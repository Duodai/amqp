<?php

namespace duodai\amqp;

use duodai\amqp\builders\ExchangeObjectBuilder;
use duodai\amqp\builders\QueueObjectBuilder;
use duodai\amqp\builders\RouteObjectBuilder;
use duodai\amqp\config\Config;
use duodai\amqp\config\ExchangeConfig;
use duodai\amqp\config\QueueConfig;
use duodai\amqp\config\RouteConfig;
use duodai\amqp\config\ServerConfig;
use duodai\amqp\exceptions\AmqpException;
use duodai\amqp\objects\Channel;
use duodai\amqp\objects\Connection;
use duodai\amqp\objects\Message;
use duodai\amqp\objects\Output;
use duodai\amqp\objects\Queue;
use duodai\amqp\objects\Route;
use Webmozart\Assert\Assert;

/**
 * Trait AMQP
 * Object oriented wrapper for php-amqp library
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
trait AmqpTrait
{
    /**
     * @var Config
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
     * @var QueueObjectBuilder
     */
    protected $queueBuilder;
    /**
     * @var ExchangeObjectBuilder
     */
    protected $exchangeBuilder;
    /**
     * @var RouteObjectBuilder
     */
    protected $routeBuilder;

    /**
     * @param Config $config
     */
    protected function initClient(Config $config)
    {
        $this->config = $config;
        $this->connections = $this->initConnections(...$this->config->getServers());
        $this->queueBuilder = $this->initQueueBuilder($this->config->getQueues());
        $this->exchangeBuilder = $this->initExchangeBuilder($this->config->getExchanges());
        $this->routeBuilder = $this->initRouteBuilder(
            $this->config->getRoutes(),
            $this->queueBuilder,
            $this->exchangeBuilder
        );
    }

    /**
     * @param ServerConfig[] ...$config
     * @return array
     */
    protected function initConnections(ServerConfig ...$config)
    {
        $connections = [];
        foreach ($config as $connectionConfig) {
            $connections[] = $this->createConnection($connectionConfig);
        }
        return $connections;
    }

    /**
     * @param ServerConfig $config
     * @return Connection
     */
    protected function createConnection(ServerConfig $config)
    {
        return new Connection($config->getConfig());
    }

    /**
     * @param QueueConfig $config
     * @return QueueObjectBuilder
     */
    protected function initQueueBuilder(QueueConfig $config)
    {
        return new QueueObjectBuilder($config);
    }

    /**
     * @param ExchangeConfig $config
     * @return ExchangeObjectBuilder
     */
    protected function initExchangeBuilder(ExchangeConfig $config)
    {
        return new ExchangeObjectBuilder($config);
    }

    /**
     * @param RouteConfig $config
     * @param QueueObjectBuilder $queueBuilder
     * @param ExchangeObjectBuilder $exchangeBuilder
     * @return RouteObjectBuilder
     */
    protected function initRouteBuilder(
        RouteConfig $config,
        QueueObjectBuilder $queueBuilder,
        ExchangeObjectBuilder $exchangeBuilder
    )
    {
        return new RouteObjectBuilder($config, $exchangeBuilder, $queueBuilder);
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
        $settings = $this->config->getSettings();
        $connections = $this->connections;
        if ($settings->isShuffleConnections()) {
            shuffle($connections);
        }
        $interval = $settings->getConnectTriesInterval();
        foreach ($connections as $connection) {
            for ($i = 0; $i < $settings->getConnectTriesLimit(); $i++) {
                if ($connection->connect()) {
                    return $connection;
                }
                usleep($interval);
                if ($settings->isConnectIntervalIncremental()) {
                    $interval = $interval + $settings->getConnectIncrement();
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
     * @param string $routeName
     * @param Channel $channel
     * @return Route
     */
    protected function buildRouteObject(string $routeName, Channel $channel)
    {
        return $this->routeBuilder->create($routeName, $channel);
    }

    /**
     * Get message from queue
     * @param string $queueName
     * @param bool|false $autoAck
     * @return Output|null
     */
    public function pull(string $queueName, $autoAck = false)
    {
        Assert::boolean($autoAck, __METHOD__ . ' error: $autoAck must be boolean, got ' . gettype($autoAck));
        $this->ensureIsConnected();
        $queue = $this->getQueue($queueName, $this->channel);
        return $queue->pull($autoAck);
    }

    /**
     * @param string $name
     * @param Channel $channel
     * @return Queue
     */
    protected function getQueue(string $name, Channel $channel)
    {
        return $this->queueBuilder->create($name, $channel);
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
     * @param string $route
     * @return Message
     */
    public function createMessage($data, string $route)
    {
        return new Message($data, $route);
    }
}
