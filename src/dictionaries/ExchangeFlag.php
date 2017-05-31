<?php


namespace duodai\amqp\dictionaries;

use duodai\amqp\common\Enum;

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