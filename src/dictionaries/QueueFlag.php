<?php


namespace Duodai\Amqp\dictionaries;

use Duodai\Amqp\common\Enum;

/**
 * Class QueueFlag
 * Queue declaration flags
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class QueueFlag extends Enum
{
    /**
     *
     */
    const FLAG_DURABLE = AMQP_DURABLE;
    /**
     *
     */
    const FLAG_PASSIVE = AMQP_PASSIVE;
    /**
     *
     */
    const FLAG_EXCLUSIVE = AMQP_EXCLUSIVE;
    /**
     *
     */
    const FLAG_AUTODELETE = AMQP_AUTODELETE;
}