<?php

namespace duodai\amqp\config;

use Duodai\Amqp\exceptions\AmqpException;

class Config
{
    const EXCHANGES_OPTION = 'exchanges';
    const QUEUES_OPTION = 'queues';
    const ROUTES_OPTION = 'routes';
    const SERVERS_OPTION = 'servers';

    /**
     * @var ServerConfig[]
     */
    protected $servers;
    /**
     * @var ExchangeConfig
     */
    protected $exchanges;
    /**
     * @var QueueConfig
     */
    protected $queues;
    /**
     * @var RouteConfig
     */
    protected $routes;


    public function __construct(array $config){
        if(empty($config[self::SERVERS_OPTION])){
            throw new AmqpException('Amqp configuration error: ' . self::SERVERS_OPTION . ' section is required');
        }
        foreach ($config[self::SERVERS_OPTION] as $server) {
            $this->servers[] = new ServerConfig($server);
        }

        if(!empty($config[self::EXCHANGES_OPTION])){
            $this->exchanges = new ExchangeConfig($config[self::EXCHANGES_OPTION]);
        }

        if(!empty($config[self::QUEUES_OPTION])){
            $this->queues = new QueueConfig($config[self::QUEUES_OPTION]);
        }
        if(!empty($config[self::ROUTES_OPTION])){
            $this->routes = new RouteConfig($config[self::ROUTES_OPTION]);
        }
    }

    /**
     * @return SettingsConfig
     */
    public function getSettings(): SettingsConfig
    {
        return $this->settings;
    }

    /**
     * @return ExchangeConfig
     */
    public function getExchanges(): ExchangeConfig
    {
        return $this->exchanges;
    }

    /**
     * @return QueueConfig
     */
    public function getQueues(): QueueConfig
    {
        return $this->queues;
    }

    /**
     * @return RouteConfig
     */
    public function getRoutes(): RouteConfig
    {
        return $this->routes;
    }
}