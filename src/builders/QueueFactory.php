<?php


namespace duodai\amqp\builders;

use duodai\amqp\config\QueueConfig;
use duodai\amqp\dictionaries\QueueFlag;
use duodai\amqp\exceptions\AmqpException;
use duodai\amqp\objects\Channel;
use duodai\amqp\objects\Queue;

/**
 * Class QueueFactory
 * Create Queue object from config
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class QueueFactory
{

    /**
     * @var QueueConfig
     */
    protected $config;

    /**
     * QueueFactory constructor.
     * @param QueueConfig $config
     */
    public function __construct(QueueConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $name
     * @param Channel $channel
     * @return Queue
     * @throws AmqpException
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPQueueException
     */
    public function create(string $name, Channel $channel)
    {
        $config = $this->config->getQueueConfig($name);
        $flags = $this->getFlags($config);
        return new Queue($name, $channel, $flags);
    }

    /**
     * Wrap flag constants from config into objects
     *
     * @param array $config
     * @return QueueFlag[]
     */
    protected function getFlags(array $config)
    {
        $flags = $config[QueueConfig::FLAGS] ?? [];
        foreach ($flags as &$flag) {
            $flag = new QueueFlag($flag);
        }
        return $flags;
    }
}
