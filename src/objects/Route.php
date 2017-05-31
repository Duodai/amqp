<?php


namespace duodai\amqp\objects;

/**
 * Class Route
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class Route
{

    /**
     * @var Exchange
     */
    protected $exchange;

    /**
     * @var RouteName
     */
    protected $routingKey;

    /**
     * @param Exchange $exchange
     * @param RouteName|null $routingKey
     */
    public function __construct(Exchange $exchange, RouteName $routingKey)
    {

        $this->exchange = $exchange;
        $this->routingKey = $routingKey;
    }

    /**
     * Get main exchange of a route
     *
     * @return Exchange
     */
    public function getExchange()
    {
        return $this->exchange;
    }

    /**
     * Get routing (binding) key
     *
     * @return RouteName
     */
    public function getRoutingKey()
    {
        return $this->routingKey;
    }
}
