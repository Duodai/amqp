<?php


namespace duodai\amqp;

use duodai\amqp\base\QueueName;
use duodai\amqp\dictionaries\QueueFlag;
use duodai\amqp\objects\Channel;
use duodai\amqp\objects\Queue;

/**
 * Class QueueObjectBuilder
 * Create Queue object from config
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class QueueObjectBuilder
{

    /**
     * @param QueueName $name
     * @param Channel $channel
     * @return Queue
     * @throws AmqpException
     */
    public function create(QueueName $name, Channel $channel)
    {
        $config = $this->getConfig($name);
        $flags = $this->getFlags($config);
        return new Queue($name, $channel, $flags);
    }

    /**
     * @param QueueName $name
     * @return QueueConfig
     */
    protected function getConfig(QueueName $name)
    {
        return new QueueConfig($name);
    }

    /**
     * Wrap flag constants from config into objects
     *
     * @param QueueConfig $config
     * @return QueueFlag[]
     * @throws AmqpException
     */
    protected function getFlags(QueueConfig $config)
    {
        $flags = $config->getParam($config::FLAGS);
        if (is_null($flags)) {
            $flags = [];
        }
        foreach ($flags as &$flag) {
            $flag = new QueueFlag($flag);
        }
        return $flags;
    }
}
