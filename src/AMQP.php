<?php
declare(strict_types=1);


namespace duodai\amqp;

use duodai\amqp\config\Config;

/**
 * Class AMQP
 * @package duodai\amqp
 */
class AMQP
{
use AmqpTrait;

    /**
     * AMQP constructor.
     * @param array $config
     * @throws exceptions\AmqpException
     */
    public function __construct(array $config)
    {
        $config = new Config($config);
        $this->initClient($config);
    }
}