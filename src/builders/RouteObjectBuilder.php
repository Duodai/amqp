<?php


namespace Duodai\Amqp\builders;

use Duodai\Amqp\config\RouteConfig;
use Duodai\Amqp\exceptions\AmqpException;
use Duodai\Amqp\objects\Channel;
use Duodai\Amqp\objects\Exchange;
use Duodai\Amqp\objects\Queue;
use Duodai\Amqp\objects\Route;


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
     * RouteObjectBuilder constructor.
     * @param RouteConfig $config
     */
    public function __construct(RouteConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Construct Route object
     *
     * @param string $route
     * @param Channel $channel
     * @return Route
     * @throws AmqpException
     */
    public function create(string $route, Channel $channel)
    {
        // Get current route config
        $config = $this->config->getRouteConfig($route);
        // Declare exchanges to avoid pushing to non-existent exchanges
        $exchanges = $this->declareExchanges($config, $channel);
        // Declare queues to avoid routing message to nowhere because of not yet declared queues
        $queues = $this->declareQueues($config, $channel);
        // Declare binds between route nodes
        $this->declareBinds($exchanges, $queues, $route);
        $mainExchange = array_shift($exchanges);
        return new Route($mainExchange, $route);
    }

    /**
     * @param array $config
     * @param Channel $channel
     * @return mixed
     * @throws AmqpException
     */
    protected function declareExchanges(array $config, Channel $channel)
    {
        $exchanges = $config[RouteConfig::SOURCE_EXCHANGES];
        if (is_null($exchanges)) {
            throw new AmqpException(__CLASS__ . '::' . __FUNCTION__ . ' error: Route config must contain at least one exchange');
        }
        foreach ($exchanges as &$exchange) {
            $exchange = $this->getExchange($this->getExchangeName($exchange), $channel);
        }
        return $exchanges;
    }

    /**
     * @param string $name
     * @param Channel $channel
     * @return Exchange
     */
    protected function getExchange(string $name, Channel $channel)
    {
        return $this->getExchangeObjectBuilder()->create($name, $channel);
    }

    /**
     * @return ExchangeObjectBuilder
     */
    protected function getExchangeObjectBuilder()
    {
        return new ExchangeObjectBuilder();
    }


    /**
     * @param RouteConfig $config
     * @param Channel $channel
     * @return mixed
     * @throws AmqpException
     */
    protected function declareQueues(RouteConfig $config, Channel $channel)
    {
        $queues = $config->getParam($config::QUEUES);

        foreach ($queues as &$queue) {
            $queue = $this->getQueue($this->getQueueName($queue), $channel);
        }
        return $queues;
    }

    /**
     * @param QueueName $name
     * @param Channel $channel
     * @return Queue
     */
    protected function getQueue(QueueName $name, Channel $channel)
    {
        return $this->getQueueObjectBuilder()->create($name, $channel);
    }

    /**
     * @return QueueObjectBuilder
     */
    protected function getQueueObjectBuilder()
    {
        return new QueueObjectBuilder();
    }

    /**
     * @param Exchange[] $exchanges
     * @param Queue[] $queues
     * @param string $routingKey
     */
    protected function declareBinds(array $exchanges, array $queues = [], string $routingKey)
    {
        /** @var Exchange $lastExchange */
        $lastExchange = null;
        // Bind exchanges. Exchanges are bound into a chain, in same order as they are in config
        // Does not support binding multiple exchanges to a fanout(approved at discussion)
        foreach ($exchanges as $exchange) {
            if (!is_null($lastExchange)) {
                $exchange->bind($exchange->getName(), $routingKey);
            }
            $lastExchange = $exchange;
        }
        // Bing queues(if any). All queues are bound to last exchange in chain
        if (!empty($queues)) {
            foreach ($queues as $queue) {
                $queue->bind($lastExchange->getName(), $routingKey);
            }
        }
    }
}
