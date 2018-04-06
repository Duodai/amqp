<?php


namespace duodai\amqp\objects;

/**
 * Class Channel
 * AMQP Channel wrapper
 */
class Channel extends \AMQPChannel
{

    /**
     * @param Connection $connection
     * @throws \AMQPConnectionException
     */
    public function __construct(Connection $connection)
    {
        parent::__construct($connection);
    }
}
