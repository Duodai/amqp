<?php


namespace duodai\amqp\dictionaries;

use duodai\amqp\common\Enum;

/**
 * Class MessageAttribute
 * Message attributes
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class MessageAttribute extends Enum
{

    /**
     * Messages are saved to disk (to survive server restart)
     */
    const DELIVERY_MODE_PERSISTENT = ['delivery_mode' => 2];
    /**
     * Messages are destroyed on server restart
     */
    const DELIVERY_MODE_NON_PERSISTENT = ['delivery_mode' => 1];

}