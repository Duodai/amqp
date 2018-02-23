<?php


namespace Duodai\Amqp\dictionaries;

use Duodai\Amqp\common\Enum;

/**
 * Class OutputFlag
 * Flags for ack/nack
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class OutputFlag extends Enum
{
    /**
     * Requeue message
     */
    const FLAG_REQUEUE = AMQP_REQUEUE;
    /**
     * Ack all unacked
     */
    const FLAG_MULTIPLE = AMQP_MULTIPLE;
}