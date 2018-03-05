<?php


namespace duodai\amqp\dictionaries;

use duodai\amqp\common\DictNoReflection;

/**
 * Class QueueFlag
 * Queue declaration flags
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class QueueFlag extends DictNoReflection
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

    /**
     * @return array
     */
    protected function getValueList(): array
    {
        return [
            self::FLAG_DURABLE,
            self::FLAG_PASSIVE,
            self::FLAG_EXCLUSIVE,
            self::FLAG_AUTODELETE
        ];
    }
}