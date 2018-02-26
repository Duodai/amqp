<?php


namespace Duodai\Amqp\config;

use Duodai\Amqp\exceptions\AmqpException;

/**
 * Class ExchangeConfig
 * Exchange declaration settings
 * @package app\components\amqp\config
 */
class ExchangeConfig
{

    const TYPE = 'type';
    const FLAGS = 'flags';

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
     * @param string $exchange
     * @return array
     * @throws AmqpException
     */
    public function getExchangeConfig(string $exchange)
    {
        $config = $this->config;
        if (!isset($config[$exchange])) {
            throw new AmqpException("Configuration for exchange {$exchange} does not exist");
        }
        return $config[$exchange];
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
