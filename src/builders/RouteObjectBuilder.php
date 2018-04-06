<?php


namespace duodai\amqp\builders;

use duodai\amqp\config\RouteConfig;
use duodai\amqp\exceptions\AmqpException;
use duodai\amqp\objects\Channel;
use duodai\amqp\objects\Exchange;
use duodai\amqp\objects\Route;

/**
 * Class RouteObjectBuilder
 * Create Route object from config
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class RouteObjectBuilder
{

    /**
     * @var RouteConfig
     */
    protected $config;

    /**
     * @var ExchangeObjectBuilder
     */
    protected $exchangeBuilder;

    /**
     * @var QueueObjectBuilder
     */
    protected $queueBuilder;

    /**
     * RouteObjectBuilder constructor.
     * @param RouteConfig $config
     * @param ExchangeObjectBuilder $exchangeBuilder
     * @param QueueObjectBuilder $queueBuilder
     *
     */
    public function __construct(RouteConfig $config, ExchangeObjectBuilder $exchangeBuilder, QueueObjectBuilder $queueBuilder)
    {
        $this->config = $config;
        $this->exchangeBuilder = $exchangeBuilder;
        $this->queueBuilder = $queueBuilder;
    }

    /**
     * @param string $route
     * @param Channel $channel
     * @return Route
     * @throws AmqpException
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     * @throws \AMQPQueueException
     */
    public function create(string $route, Channel $channel)
    {
        // Get current route config
        $config = $this->config->getRouteConfig($route);
        if (empty($config[RouteConfig::SOURCE_EXCHANGE])) {
            throw new AmqpException(__METHOD__ . ' error: Route config must contain source exchange');
        }
        $sourceExchange = $this->declareSourceExchange($config[RouteConfig::SOURCE_EXCHANGE], $channel);
        // Declare exchanges to avoid pushing to non-existent exchanges
        $exchangeNames = $config[RouteConfig::DESTINATION_EXCHANGES] ?? [];
        $exchanges = $this->declareExchanges($exchangeNames, $channel);
        // Declare queues to avoid routing message to nowhere because of not yet declared queues
        $queueNames = $config[RouteConfig::DESTINATION_QUEUES] ?? [];
        $queues = $this->declareQueues($queueNames, $channel);
        // Declare binds between route nodes
        $this->declareBinds($sourceExchange, $exchanges, $queues, $route);
        return new Route($sourceExchange, $route);
    }

    /**
     * @param string $exchangeName
     * @param Channel $channel
     * @return Exchange
     * @throws AmqpException
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     */
    protected function declareSourceExchange(string $exchangeName, Channel $channel)
    {
        return $this->exchangeBuilder->create($exchangeName, $channel);
    }

    /**
     * @param array $exchangeNames
     * @param Channel $channel
     * @return array
     * @throws AmqpException
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     */
    protected function declareExchanges(array $exchangeNames, Channel $channel)
    {
        foreach ($exchangeNames as $exchange) {
            $exchanges[] = $this->exchangeBuilder->create($exchange, $channel);
        }
        return $exchanges ?? [];
    }

    /**
     * @param array $queueNames
     * @param Channel $channel
     * @return array
     * @throws AmqpException
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPQueueException
     */
    protected function declareQueues(array $queueNames, Channel $channel)
    {
        foreach ($queueNames as $queue) {
            $queueObjects[] = $this->queueBuilder->create($queue, $channel);
        }
        return $queueObjects ?? [];
    }

    /**
     * @param Exchange $sourceExchange
     * @param array $exchanges
     * @param array $queues
     * @param string $routingKey
     */
    protected function declareBinds(Exchange $sourceExchange, array $exchanges, array $queues, string $routingKey)
    {
        /** @var Exchange $lastExchange */
        $lastExchange = null;
        if (!empty($exchanges)) {
            foreach ($exchanges as $exchange) {
                $exchange->bind($sourceExchange->getName(), $routingKey);
            }
        }
        // Bing queues(if any). All queues are bound to last exchange in chain
        if (!empty($queues)) {
            foreach ($queues as $queue) {
                $queue->bind($sourceExchange->getName(), $routingKey);
            }
        }
    }

    /**
     * @param string $name
     * @param Channel $channel
     * @return Exchange
     * @throws AmqpException
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     */
    protected function getExchange(string $name, Channel $channel)
    {
        return $this->exchangeBuilder->create($name, $channel);
    }
}
