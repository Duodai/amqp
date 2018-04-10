<?php


namespace duodai\amqp\builders;

use duodai\amqp\config\ExchangeConfig;
use duodai\amqp\dictionaries\ExchangeFlag;
use duodai\amqp\dictionaries\ExchangeType;
use duodai\amqp\exceptions\AmqpException;
use duodai\amqp\objects\Channel;
use duodai\amqp\objects\Exchange;

/**
 * Class ExchangeFactory
 * Create exchange object from config
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class ExchangeFactory
{

    /**
     * @var ExchangeConfig
     */
    protected $config;

    /**
     * ExchangeFactory constructor.
     * @param ExchangeConfig $config
     */
    public function __construct(ExchangeConfig $config)
    {
        $this->config = $config;
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
    public function create(string $name, Channel $channel)
    {
        $config = $this->config->getExchangeConfig($name);
        $type = $this->getType($config);
        $flags = $this->getFlags($config);
        return new Exchange($name, $type, $channel, $flags);
    }

    /**
     * Wrap exchange type into object
     * @param array $config
     * @return ExchangeType
     * @throws AmqpException
     */
    protected function getType(array $config)
    {
        $type = $config[ExchangeConfig::TYPE] ?? null;
        if (is_null($type)) {
            throw new AmqpException(__CLASS__ . ' error: Invalid exchange config. Missing required param "type"');
        }
        return new ExchangeType($type);
    }

    /**
     * Wrap exchange flags into objects
     * @param array $config
     * @return ExchangeFlag[]
     */
    protected function getFlags(array $config)
    {
        $flags = $config[ExchangeConfig::FLAGS] ?? [];
        foreach ($flags as &$flag) {
            $flag = new ExchangeFlag($flag);
        }
        return $flags;
    }
}
