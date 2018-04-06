<?php


namespace duodai\amqp\config;

use duodai\amqp\exceptions\AmqpException;

/**
 * Class RouteConfig
 * Pre-configured routes.
 * @package app\components\amqp\config
 */
class RouteConfig
{

    /**
     *
     */
    const SOURCE_EXCHANGE = 'sourceExchange';
    /**
     *
     */
    const DESTINATION_EXCHANGES = 'destExchanges';
    /**
     *
     */
    const DESTINATION_QUEUES = 'destQueues';

    /**
     * @var array
     */
    protected $config;


    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $route
     * @return array
     * @throws AmqpException
     */
    public function getRouteConfig(string $route)
    {
        $config = $this->config;
        if (!isset($config[$route])) {
            throw new AmqpException("Configuration for route {$route} does not exist");
        }
        return $config[$route];
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return array
     */
    public function getList()
    {
        return array_keys($this->config);
    }

}
