<?php


namespace duodai\amqp\objects;

use duodai\amqp\dictionaries\QueueFlag;
use duodai\amqp\exceptions\AmqpException;

/**
 * Class Queue
 * AMQP queue
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class Queue
{

    /**
     * Auto-ack flag for pull method
     */
    const FLAG_AUTO_ACK = AMQP_AUTOACK;
    /**
     *
     */
    const NO_FLAGS = AMQP_NOPARAM;

    /**
     * @var
     */
    protected $flags;
    /**
     * @var array
     */
    protected $bindings = [];
    /**
     * @var string
     */
    protected $name;
    /**
     * @var \AMQPQueue
     */
    protected $component;

    /**
     * Queue constructor.
     * @param string $name
     * @param Channel $channel
     * @param array $flags
     * @throws AmqpException
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPQueueException
     */
    public function __construct(string $name, Channel $channel, array $flags = [])
    {
        $this->component = $this->createComponent($channel);
        $this->setName($name);
        $this->setFlags($flags);
        $this->component->declareQueue();
    }

    /**
     * @param Channel $channel
     * @return \AMQPQueue
     * @throws \AMQPConnectionException
     * @throws \AMQPQueueException
     */
    protected function createComponent(Channel $channel)
    {
        return new \AMQPQueue($channel);
    }

    /**
     * @param string $name
     */
    protected function setName(string $name)
    {
        $this->component->setName($name);
    }

    /**
     * @param QueueFlag[] $flags
     * @throws AmqpException
     */
    protected function setFlags(array $flags)
    {
        $flagsBitMask = self::NO_FLAGS;
        foreach ($flags as $flag) {
            if ($flag instanceof QueueFlag) {
                $flagsBitMask += $flag->val();
            } else {
                throw new AmqpException(__CLASS__ . 'error: $flags must be an array of QueueFlag objects');
            }
        }
        $this->component->setFlags($flagsBitMask);
    }

    /**
     * @param bool $autoAck
     * @return Output|null
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     */
    public function pull($autoAck = false)
    {
        $response = $this->component->get($autoAck ? self::FLAG_AUTO_ACK : AMQP_NOPARAM);
        if (!$response) {
            return null;
        }
        return new Output($this, $response);
    }

    /**
     * @param callable $callback
     * @param string $consumerTag
     * @param bool $autoAck
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     */
    public function consume(Callable $callback, string $consumerTag, $autoAck = false)
    {
        $this->component->consume($callback, $autoAck ? self::FLAG_AUTO_ACK : AMQP_NOPARAM, $consumerTag);
    }

    /**
     * @param string $consumerTag
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     */
    public function cancel(string $consumerTag)
    {
        $this->component->cancel($consumerTag);
    }

    /**
     * @param Output $output
     * @return bool
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     */
    public function ack(Output $output)
    {
        $tag = $output->getDeliveryTag();
        $flags = $output->getFlags();
        return $this->component->ack($tag, $flags);
    }

    /**
     * @param Output $output
     * @return bool
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     */
    public function nack(Output $output)
    {
        $tag = $output->getDeliveryTag();
        $flags = $output->getFlags();
        return $this->component->nack($tag, $flags);
    }

    /**
     * @param string $exchangeName
     * @param string $routeName
     * @return bool
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     */
    public function bind(string $exchangeName, string $routeName)
    {
        return $this->component->bind($exchangeName, $routeName);
    }

    /**
     * @return bool
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     */
    public function purge() :bool
    {
        return $this->component->purge();
    }

    /**
     * @param bool $ifUnused If true, queue will only be deleted if no consumers currently use it
     * @return int Number of deleted messages
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     */
    public function delete($ifUnused = false):int
    {
        return $this->component->delete($ifUnused ? AMQP_IFUNUSED : AMQP_NOPARAM);
    }

    /**
     * @param Route $route
     * @return bool
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     */
    public function unbind(Route $route)
    {
        return $this->component->unbind($route->getExchange()->getName(), $route->getRoutingKey());
    }
}
