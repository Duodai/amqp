<?php


namespace Duodai\Amqp;

use duodai\amqp\base\ExchangeName;
use duodai\amqp\base\RouteName;
use Duodai\Amqp\exceptions\AmqpException;
use duodai\amqp\objects\Channel;
use duodai\amqp\objects\Exchange;
use duodai\amqp\objects\Queue;
use duodai\amqp\objects\Route;


/**
 * Class RouteObjectBuilder
 * Create Route object from config
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class RouteObjectBuilder
{

    /**
     * Construct Route object
     *
     * @param RouteName $route
     * @param Channel $channel
     * @return Route
     * @throws AmqpException
     */
    public function create(RouteName $route, Channel $channel)
    {
        // Get current route config
        $config = $this->getConfig($route);
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
     * @param RouteName $route
     * @return RouteConfig
     */
    protected function getConfig(RouteName $route)
    {
        return new RouteConfig($route);
    }

    /**
     * @param RouteConfig $config
     * @param Channel $channel
     * @return mixed
     * @throws AmqpException
     */
    protected function declareExchanges(RouteConfig $config, Channel $channel)
    {
        $exchanges = $config->getParam($config::EXCHANGES);
        if (is_null($exchanges)) {
            throw new AmqpException(__CLASS__ . '::' . __FUNCTION__ . ' error: Route config must contain at least one exchange');
        }
        foreach ($exchanges as &$exchange) {
            $exchange = $this->getExchange($this->getExchangeName($exchange), $channel);
        }
        return $exchanges;
    }

    /**
     * @param ExchangeName $name
     * @param Channel $channel
     * @return Exchange
     */
    protected function getExchange(ExchangeName $name, Channel $channel)
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
     * @param $name
     * @return ExchangeName
     */
    protected function getExchangeName($name)
    {
        return new ExchangeName($name);
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
     * @param $name
     * @return QueueName
     */
    private function getQueueName($name)
    {
        return new QueueName($name);
    }

    /**
     * @param Exchange[] $exchanges
     * @param Queue[] $queues
     * @param RouteName $routingKey
     */
    protected function declareBinds(array $exchanges, array $queues = [], RouteName $routingKey)
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
