<?php


namespace Duodai\Amqp\objects;

/**
 * Class Channel
 * AMQP Channel wrapper
 */
class Channel extends \AMQPChannel
{

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        parent::__construct($connection);
    }
}
