<?php


namespace duodai\amqp\config;

use duodai\amqp\exceptions\AmqpException;

/**
 * Class QueueConfig
 * Queue declaration settings
 * @package app\components\amqp\config
 */
class QueueConfig
{

    /**
     * Queue flags. Constants from QueueFlag class
     */
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
     * @param string $queue
     * @return array
     * @throws AmqpException
     */
    public function getQueueConfig(string $queue)
    {
        $config = $this->config;
        if (!isset($config[$queue])) {
            throw new AmqpException("Configuration for queue {$queue} does not exist");
        }
        return $config[$queue];
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
