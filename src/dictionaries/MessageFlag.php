<?php


namespace duodai\amqp\dictionaries;

use duodai\amqp\common\Enum;

/**
 * Class MessageFlag
 * Message flags
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class MessageFlag extends Enum
{
    /**
     * Return message instead of dropping it, in case of inaccessible route
     */
    const FLAG_MANDATORY = AMQP_MANDATORY;
    /**
     * Place message to a queue beginning instead of an end
     */
    const FLAG_IMMEDIATE = AMQP_IMMEDIATE;
}