<?php


namespace Duodai\Amqp\objects;


use Duodai\Amqp\dictionaries\ExchangeFlag;
use Duodai\Amqp\dictionaries\ExchangeType;
use Duodai\Amqp\exceptions\AmqpException;

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
     * @var string
     */
    protected $name;

    /**
     * @param string $name
     * @param ExchangeType $type
     * @param Channel $channel
     * @param ExchangeFlag[] $flags
     * @throws AmqpException
     */
    public function __construct(string $name, ExchangeType $type, Channel $channel, array $flags = [])
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
        $result = $this->component->publish(
            $message->getBody(),
            $message->getRoute(),
            $message->getFlags(),
            $message->getAttributes()
        );
        return is_null($result) ? true : $result;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set exchange name
     *
     * @param string $name
     */
    protected function setName(string $name)
    {
        $this->name = $name;
        $this->component->setName($name);
    }

    /**
     * Declare binding to another exchange
     * @param string $exchangeName
     * @param string $routeName
     * @return bool
     */
    public function bind(string $exchangeName, string $routeName)
    {
        return $this->component->bind($exchangeName, $routeName);
    }
}
