<?php


namespace duodai\amqp\objects;

use duodai\amqp\AmqpException;
use duodai\amqp\dictionaries\ExchangeFlag;
use duodai\amqp\dictionaries\ExchangeType;

/**
 * Class Exchange
 * AMQP exchange
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class Exchange
{

    /**
     * Empty flags bitmask
     */
    const NO_FLAGS = AMQP_NOPARAM;

    /**
     * @var
     */
    protected $flags = [];

    /**
     * @var \AMQPExchange
     */
    protected $component;

    /**
     * @var ExchangeType
     */
    protected $type;

    /**
     * @var ExchangeName
     */
    protected $name;

    /**
     * @param ExchangeName $name
     * @param ExchangeType $type
     * @param Channel $channel
     * @param ExchangeFlag[] $flags
     * @throws AmqpException
     */
    public function __construct(ExchangeName $name, ExchangeType $type, Channel $channel, array $flags = [])
    {
        $this->component = $this->createComponent($channel);
        $this->setName($name);
        $this->setType($type);
        $this->setFlags($flags);
        $this->component->declareExchange();
    }

    /**
     * @param Channel $channel
     * @return \AMQPExchange
     */
    protected function createComponent(Channel $channel)
    {
        return new \AMQPExchange($channel);
    }

    /**
     * @param ExchangeType $type
     * @return bool
     */
    protected function setType(ExchangeType $type)
    {
        $this->component->setType($type->val());
    }

    /**
     * @param ExchangeFlag[] $flags
     * @throws AmqpException
     */
    private function setFlags(array $flags)
    {
        $flagsBitMask = self::NO_FLAGS;
        foreach ($flags as $flag) {
            if ($flag instanceof ExchangeFlag) {
                $flagsBitMask += $flag->val();
            } else {
                throw new AmqpException(__CLASS__ . 'error: $flags must be an array of ExchangeFlag objects');
            }
        }
        $this->component->setFlags($flagsBitMask);
    }

    /**
     * Publish message
     * @param Message $message
     * @return bool
     */
    public function push(Message $message)
    {
        $this->component->publish(
            $message->getBody(),
            $message->getRoute()->val(),
            $message->getFlags(),
            $message->getAttributes()
        );
        /*
         TODO Currently amqp extension returns null instead of boolean.
         If anything changes - make normal return here.
         */
        return true;
    }

    /**
     * @return ExchangeName
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set exchange name
     *
     * @param ExchangeName $name
     */
    protected function setName(ExchangeName $name)
    {
        $this->name = $name;
        $this->component->setName($name->val());
    }

    /**
     * Declare binding to another exchange
     * @param ExchangeName $exchangeName
     * @param RouteName $routeName
     * @return bool
     */
    public function bind(ExchangeName $exchangeName, RouteName $routeName)
    {
        return $this->component->bind($exchangeName->val(), $routeName->val());
    }
}
