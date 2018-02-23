<?php


namespace Duodai\Amqp\builders;

use Duodai\Amqp\config\QueueConfig;
use Duodai\Amqp\dictionaries\QueueFlag;
use Duodai\Amqp\objects\Channel;
use Duodai\Amqp\objects\Queue;

/**
 * Class QueueObjectBuilder
 * Create Queue object from config
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class QueueObjectBuilder
{

    /**
     * @var QueueConfig
     */
    protected $config;

    /**
     * QueueObjectBuilder constructor.
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
