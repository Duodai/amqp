<?php


namespace Duodai\Amqp\config;

use Duodai\Amqp\exceptions\AmqpException;

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
    const SOURCE_EXCHANGES = 'sourceExchanges';
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

    public function getConfig()
    {
        return $this->config;
    }

    public function getList()
    {
        return array_keys($this->config);
    }

}
