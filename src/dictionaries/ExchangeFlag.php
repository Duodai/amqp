<?php


namespace Duodai\Amqp\dictionaries;

use Duodai\Amqp\common\Enum;

/**
 * Class ExchangeFlag
 * List of exchange flags
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class ExchangeFlag extends Enum
{
    /**
     * Persistent exchange declaration
     * (messages are not persistent)
     */
    const FLAG_DURABLE = AMQP_DURABLE;
    /**
     *
     */
    const FLAG_PASSIVE = AMQP_PASSIVE;
}