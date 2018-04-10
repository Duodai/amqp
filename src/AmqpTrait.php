<?php

namespace duodai\amqp;

use duodai\amqp\builders\ExchangeFactory;
use duodai\amqp\builders\QueueFactory;
use duodai\amqp\builders\RouteFactory;
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
     * @var QueueFactory
     */
    protected $queueBuilder;
    /**
     * @var ExchangeFactory
     */
    protected $exchangeBuilder;
    /**
     * @var RouteFactory
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
     * @return QueueFactory
     */
    protected function initQueueBuilder(QueueConfig $config)
    {
        return new QueueFactory($config);
    }

    /**
     * @param ExchangeConfig $config
     * @return ExchangeFactory
     */
    protected function initExchangeBuilder(ExchangeConfig $config)
    {
        return new ExchangeFactory($config);
    }

    /**
     * @param RouteConfig $config
     * @param QueueFactory $queueBuilder
     * @param ExchangeFactory $exchangeBuilder
     * @return RouteFactory
     */
    protected function initRouteBuilder(
        RouteConfig $config,
        QueueFactory $queueBuilder,
        ExchangeFactory $exchangeBuilder
    )
    {
        return new RouteFactory($config, $exchangeBuilder, $queueBuilder);
    }

    /**
     * Put message into queue
     * @param Message $message
     * @return bool
     * @throws AmqpException
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     * @throws \AMQPQueueException
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
     * @throws AmqpException
     * @throws \AMQPConnectionException
     */
    protected function ensureIsConnected()
    {
        if (!isset($this->channel) || !$this->channel->isConnected()) {
            $this->connect();
        }
    }

    /**
     * @throws AmqpException
     * @throws \AMQPConnectionException
     */
    protected function connect()
    {
        $connection = $this->getAccessibleConnection();
        $this->channel = $this->createChannel($connection);
    }

    /**
     * @return Connection
     * @throws AmqpException
     * @throws \AMQPConnectionException
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
     * @throws \AMQPConnectionException
     */
    protected function createChannel(Connection $connection)
    {
        return new Channel($connection);
    }

    /**
     * @param string $routeName
     * @param Channel $channel
     * @return Route
     * @throws AmqpException
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     * @throws \AMQPQueueException
     */
    protected function buildRouteObject(string $routeName, Channel $channel)
    {
        return $this->routeBuilder->create($routeName, $channel);
    }

    /**
     * @param string $queueName
     * @param bool $autoAck
     * @return Output|null
     * @throws AmqpException
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPQueueException
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
     * @throws AmqpException
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPQueueException
     */
    protected function getQueue(string $name, Channel $channel)
    {
        return $this->queueBuilder->create($name, $channel);
    }

    /**
     * Confirm message removal
     * @param Output $output
     * @return bool
     * @throws AmqpException
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
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
     * @throws AmqpException
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     */
    public function nack(Output $output)
    {
        $this->ensureIsConnected();
        return $output->nack();
    }

    /**
     * Generate new Message object
     * @param $data
     * @param string $route
     * @return Message
     * @throws AmqpException
     */
    public function createMessage($data, string $route)
    {
        return new Message($data, $route);
    }
}
