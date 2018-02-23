<?php


namespace Duodai\Amqp\objects;

use Duodai\Amqp\dictionaries\QueueFlag;
use Duodai\Amqp\exceptions\AmqpException;

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
     * @param string $name
     * @param Channel $channel
     * @param QueueFlag[] $flags
     * @throws AmqpException
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
     * @param Output $output
     * @return bool
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
     */
    public function bind(string $exchangeName, string $routeName)
    {
        return $this->component->bind($exchangeName, $routeName);
    }
}
