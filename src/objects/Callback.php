<?php
declare(strict_types=1);


namespace duodai\amqp\objects;


abstract class Callback
{
    public function execute(\AMQPEnvelope $response, Queue $queue)
    {
        return $this->process(new Output($queue, $response));
    }

    abstract protected function process(Output $output);
}