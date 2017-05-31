<?php


namespace duodai\amqp\dictionaries;

use duodai\amqp\common\Enum;

/**
 * Class ExchangeType
 * Exchange types list
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class ExchangeType extends Enum
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
}