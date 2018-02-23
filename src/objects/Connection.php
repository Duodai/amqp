<?php


namespace Duodai\Amqp\objects;

/**
 * Class Connection
 * AMQP connection wrapper
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class Connection extends \AMQPConnection
{

    /**
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
    }
}
