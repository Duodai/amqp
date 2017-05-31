<?php


namespace duodai\amqp\objects;

use duodai\amqp\dictionaries\OutputFlag;

/**
 * Class Output
 * Container for amqp queue response data
 */
class Output
{

    /**
     * @var string
     */
    protected $message;
    /**
     * @var Queue
     */
    protected $queue;
    /**
     * @var string
     */
    protected $deliveryTag;
    /**
     * @var array
     */
    protected $flags = [];

    /**
     * @param Queue $queue
     * @param \AMQPEnvelope $envelope
     */
    public function __construct(Queue $queue, \AMQPEnvelope $envelope)
    {
        $this->queue = $queue;
        $this->message = $envelope->getBody();
        $this->deliveryTag = $envelope->getDeliveryTag();
    }

    /**
     * Confirm successful delivery
     * @return bool
     */
    public function ack()
    {
        return $this->queue->ack($this);
    }

    /**
     * Confirm failed delivery
     * @return bool
     */
    public function nack()
    {
        return $this->queue->nack($this);
    }

    /**
     * Add flag to confirmation query
     *
     * @param OutputFlag $flag
     */
    public function setFlag(OutputFlag $flag)
    {
        $this->flags[$flag->val()] = $flag->val();
    }

    /**
     * Get all confirmation flags in bit-mask format
     *
     * @return number
     */
    public function getFlags()
    {
        return array_sum($this->flags);
    }

    /**
     * Get message body
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get delivery tag for delivery confirmation
     *
     * @return string
     */
    public function getDeliveryTag()
    {
        return $this->deliveryTag;
    }
}
