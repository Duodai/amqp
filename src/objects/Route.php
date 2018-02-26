<?php


namespace duodai\amqp\objects;

/**
 * Class Route
 */
class Route
{

    /**
     * @var Exchange
     */
    protected $exchange;

    /**
     * @var string
     */
    protected $routingKey;

    /**
     * @param Exchange $exchange
     * @param string $routingKey
     */
    public function __construct(Exchange $exchange, string $routingKey)
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
     * @return string
     */
    public function getRoutingKey()
    {
        return $this->routingKey;
    }
}
