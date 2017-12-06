<?php


namespace duodai\amqp;

use duodai\amqp\base\ExchangeName;
use duodai\amqp\dictionaries\ExchangeFlag;
use duodai\amqp\dictionaries\ExchangeType;
use duodai\amqp\objects\Channel;
use duodai\amqp\objects\Exchange;

/**
 * Class ExchangeObjectBuilder
 * Create exchange object from config
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class ExchangeObjectBuilder
{

    /**
     * @param ExchangeName $name
     * @param Channel $channel
     * @return Exchange
     * @throws AmqpException
     */
    public function create(ExchangeName $name, Channel $channel)
    {
        $config = $this->getConfig($name);
        $type = $this->getType($config);
        $flags = $this->getFlags($config);
        return new Exchange($name, $type, $channel, $flags);
    }

    /**
     * Get exchange config
     * @param ExchangeName $name
     * @return ExchangeConfig
     */
    protected function getConfig(ExchangeName $name)
    {
        return new ExchangeConfig($name);
    }

    /**
     * Wrap exchange type into object
     * @param ExchangeConfig $config
     * @return \duodai\amqp\dictionaries\ExchangeType
     * @throws AmqpException
     */
    protected function getType(ExchangeConfig $config)
    {
        $type = $config->getParam($config::TYPE);
        if (is_null($type)) {
            throw new AmqpException(__CLASS__ . ' error: Invalid exchange config. Missing required param "type"');
        }
        return new ExchangeType($type);
    }

    /**
     * Wrap exchange flags into objects
     * @param ExchangeConfig $config
     * @return ExchangeFlag[]
     */
    protected function getFlags(ExchangeConfig $config)
    {
        $flags = $config->getParam($config::FLAGS);
        if (is_null($flags)) {
            $flags = [];
        }
        foreach ($flags as &$flag) {
            $flag = new ExchangeFlag($flag);
        }
        return $flags;
    }
}
