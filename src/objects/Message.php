<?php


namespace duodai\amqp\objects;

use duodai\amqp\exceptions\AmqpException;
use duodai\amqp\dictionaries\MessageAttribute;
use duodai\amqp\dictionaries\MessageFlag;

/**
 * Class Message
 * Queue message template
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class Message
{

    /**
     * @var string
     */
    protected $body;
    /**
     * @var string
     */
    protected $route;
    /**
     * @var array
     */
    protected $flags = [];
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @param $body
     * @param string $route
     * @throws AmqpException
     */
    public function __construct($body, string $route)
    {
        if (!is_string($body)) {
            throw new AmqpException(__CLASS__ . '::' . __FUNCTION__ . ' error: $body must be a string.');
        }
        $this->defaultAttributes();
        $this->body = $body;
        $this->route = $route;
    }

    /**
     * Default attribute values for new messages
     */
    protected function defaultAttributes()
    {
        $this->setAttribute(new MessageAttribute(MessageAttribute::DELIVERY_MODE_PERSISTENT));
    }

    /**
     * Add message attribute
     * @param MessageAttribute $attribute
     */
    public function setAttribute(MessageAttribute $attribute)
    {
        $this->attributes = array_merge($this->attributes, $attribute->val());
    }

    /**
     * Add message flag
     * @param MessageFlag $flag
     */
    public function setFlag(MessageFlag $flag)
    {
        $this->flags[$flag->val()] = $flag->val();
    }

    /**
     * Get message flags in bit-mask format
     * @return number
     */
    public function getFlags()
    {
        return array_sum($this->flags);
    }

    /**
     * Get message body
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get route id
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Get all message attributes
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }


}
