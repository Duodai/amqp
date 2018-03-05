<?php


namespace duodai\amqp\dictionaries;

use duodai\amqp\common\DictNoReflection;

/**
 * Class MessageFlag
 * Message flags
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class MessageFlag extends DictNoReflection
{
    /**
     * Return message instead of dropping it, in case of inaccessible route
     */
    const FLAG_MANDATORY = AMQP_MANDATORY;
    /**
     * Place message to a queue beginning instead of an end
     */
    const FLAG_IMMEDIATE = AMQP_IMMEDIATE;

    /**
     * @return array
     */
    protected function getValueList(): array
    {
        return [
            self::FLAG_MANDATORY,
            self::FLAG_IMMEDIATE
        ];
    }
}