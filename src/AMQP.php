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

    public function __construct(array $config)
    {
        $config = new Config($config);
        $this->init($config);
    }
}