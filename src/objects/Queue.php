<?php


namespace duodai\amqp\objects;

use duodai\amqp\AmqpException;
use duodai\amqp\dictionaries\QueueFlag;

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
     * @var QueueName
     */
    protected $name;
    /**
     * @var \AMQPQueue
     */
    protected $component;


    /**
     * @param QueueName $name
     * @param Channel $channel
     * @param QueueFlag[] $flags
     * @throws AmqpException
     */
    public function __construct(QueueName $name, Channel $channel, array $flags = [])
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
     * @param QueueName $name
     */
    protected function setName(QueueName $name)
    {
        $this->component->setName($name->val());
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
     * @param ExchangeName $exchangeName
     * @param RouteName $routeName
     * @return bool
     */
    public function bind(ExchangeName $exchangeName, RouteName $routeName)
    {
        return $this->component->bind($exchangeName->val(), $routeName->val());
    }
}
