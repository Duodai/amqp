<?php


namespace duodai\amqp\objects;


use duodai\amqp\dictionaries\ExchangeFlag;
use duodai\amqp\dictionaries\ExchangeType;
use duodai\amqp\exceptions\AmqpException;

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
     * Exchange constructor.
     * @param string $name
     * @param ExchangeType $type
     * @param Channel $channel
     * @param array $flags
     * @throws AmqpException
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
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
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     */
    protected function createComponent(Channel $channel): \AMQPExchange
    {
        return new \AMQPExchange($channel);
    }

    /**
     * @param ExchangeType $type
     */
    protected function setType(ExchangeType $type): void
    {
        $this->component->setType($type->val());
    }

    /**
     * @param ExchangeFlag[] $flags
     * @throws AmqpException
     */
    private function setFlags(array $flags): void
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
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     */
    public function push(Message $message): bool
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set exchange name
     *
     * @param string $name
     */
    protected function setName(string $name): void
    {
        $this->name = $name;
        $this->component->setName($name);
    }

    /**
     * Declare binding to another exchange
     * @param string $exchangeName
     * @param string $routeName
     * @return bool
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     */
    public function bind(string $exchangeName, string $routeName): bool
    {
        return $this->component->bind($exchangeName, $routeName);
    }

    /**
     * @param Route $route
     * @return bool
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     */
    public function unbind(Route $route): bool
    {
        return $this->component->unbind($route->getExchange()->getName(), $route->getRoutingKey());
    }

    /**
     * @param bool $ifUnused
     * @return bool
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     */
    public function delete($ifUnused = false): bool
    {
        return $this->component->delete($this->getName(), $ifUnused ? AMQP_IFUNUSED : AMQP_NOPARAM);
    }
}
