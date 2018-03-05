<?php


namespace duodai\amqp\dictionaries;

use duodai\amqp\common\DictNoReflection;

/**
 * Class ExchangeFlag
 * List of exchange flags
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class ExchangeFlag extends DictNoReflection
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

    /**
     * @return array
     */
    protected function getValueList(): array
    {
       return [
           self::FLAG_DURABLE,
           self::FLAG_PASSIVE
       ];
    }
}