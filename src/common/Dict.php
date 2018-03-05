<?php


namespace duodai\amqp\common;

/**
 * Class Dict
 * Base class for constant value lists
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
abstract class Dict
{
    /**
     * Current value
     *
     * @var mixed
     */
    private $value;

    /**
     * @param $value
     * @throws \Exception
     */
    final public function __construct($value)
    {
        if (ReflectionHelper::isClassConstantValue($value, $this)) {
            $this->value = $value;
        } else {
            throw new \InvalidArgumentException($this->errorMessage($value));
        }
    }

    protected function errorMessage($value)
    {
        return get_called_class() . ' error: trying to instantiate class with an invalid value ' . is_scalar($value) ? $value : gettype($value) . '. Use class constants';
    }

    /**
     * @return array
     */
    public static function constants()
    {
        return ReflectionHelper::constants(get_called_class());
    }

    /**
     * Get stored value
     *
     * @return mixed
     */
    final public function val()
    {
        return $this->value;
    }
}