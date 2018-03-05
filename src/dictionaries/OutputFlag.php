<?php


namespace duodai\amqp\dictionaries;

use duodai\amqp\common\DictNoReflection;

/**
 * Class OutputFlag
 * Flags for ack/nack
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class OutputFlag extends DictNoReflection
{
    /**
     * Requeue message
     */
    const FLAG_REQUEUE = AMQP_REQUEUE;
    /**
     * Ack all unacked
     */
    const FLAG_MULTIPLE = AMQP_MULTIPLE;

    /**
     * @return array
     */
    protected function getValueList(): array
    {
        return [
            self::FLAG_REQUEUE,
            self::FLAG_MULTIPLE
        ];
    }
}