<?php


namespace duodai\amqp\common;

/**
 * Helper to simplify usage of ReflectionClass
 * Class ReflectionHelper
 * @author Michael Janus <abyssal@mail.ru>
 */
class ReflectionHelper
{

    /**
     * Returns class name without namespace
     * @param $object
     * @return string
     * @throws \ReflectionException
     */
    public static function shortClassName($object)
    {
        $reflection = new \ReflectionClass($object);
        return $reflection->getShortName();
    }

    /**
     * Check if there is a class constant with given value
     * @param $value
     * @param $object
     * @return bool
     * @throws \ReflectionException
     */
    public static function isClassConstantValue($value, $object)
    {
        return in_array($value, self::constants($object));
    }

    /**
     * Get class constants as a named array
     * @param $object
     * @return array
     * @throws \ReflectionException
     */
    public static function constants($object)
    {
        $reflection = new \ReflectionClass($object);
        return $reflection->getConstants();
    }

    /**
     * Check if there is a class constant with given name
     * @param $value
     * @param $object
     * @return bool
     * @throws \ReflectionException
     */
    public static function isClassConstantName($value, $object)
    {
        return in_array(strtoupper($value), array_keys(self::constants($object)));
    }
}
