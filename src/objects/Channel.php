<?php


namespace duodai\amqp\objects;

/**
 * Class Channel
 * AMQP Channel wrapper
 * @author Michael Janus <mailto:abyssal@mail.ru>
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