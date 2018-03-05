<?php


namespace duodai\amqp\dictionaries;

use duodai\amqp\common\DictNoReflection;

/**
 * Class ExchangeType
 * Exchange types list
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class ExchangeType extends DictNoReflection
{
    /**
     *
     */
    const TYPE_DIRECT = AMQP_EX_TYPE_DIRECT;
    /**
     *
     */
    const TYPE_FANOUT = AMQP_EX_TYPE_FANOUT;
    /**
     *
     */
    const TYPE_TOPIC = AMQP_EX_TYPE_TOPIC;
    /**
     *
     */
    const TYPE_HEADERS = AMQP_EX_TYPE_HEADERS;

    /**
     * @return array
     */
    protected function getValueList(): array
    {
        return [
            self::TYPE_DIRECT,
            self::TYPE_FANOUT,
            self::TYPE_TOPIC,
            self::TYPE_HEADERS
        ];
    }
}