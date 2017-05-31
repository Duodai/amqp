<?php


namespace duodai\amqp;

use duodai\amqp\common\ReflectionHelper;

/**
 * Class BaseConfig
 * Base class for configurations(Only to prevent code duplication).
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
abstract class BaseConfig
{

    /**
     * Current config branch. Fill it in __construct
     * @var array
     */
    protected $config;

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get param from config. Use class constants as param name.
     *
     * @param string $param
     * @return mixed
     * @throws AmqpException
     */
    public function getParam($param)
    {
        if (!ReflectionHelper::isClassConstantValue($param, $this)) {
            throw new AmqpException(__CLASS__ . '::' . __FUNCTION__ . ' error: argument must be a class constant value');
        }
        return isset($this->config[$param]) ? $this->config[$param] : null;
    }

    /**
     * Write config here
     *
     * @return array
     */
    abstract protected function configuration();
}
