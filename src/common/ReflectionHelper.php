<?php


namespace duodai\amqp\common;

/**
 * Helper to simplify usage of ReflectionClass
 * Class ReflectionHelper
 * @author Michael Janus <abyssal@mail.ru>
 */
class ReflectionHelper
{ //TODO Make stand-alone package for helpers and common classes

    /**
     * Returns class name without namespace
     *
     * @param object $object
     * @return string
     */
    public static function shortClassName($object)
    {
        $reflection = new \ReflectionClass($object);
        return $reflection->getShortName();

    }

    /**
     * Check if there is a class constant with given value
     *
     * @param mixed $value
     * @param object $object
     * @return bool
     */
    public static function isClassConstantValue($value, $object)
    {
        return in_array($value, self::constants($object));
    }

    /**
     * Get class constants as a named array
     * @param object|string $object
     * @return array
     */
    public static function constants($object)
    {
        $reflection = new \ReflectionClass($object);
        return $reflection->getConstants();
    }

    /**
     * Check if there is a class constant with given name
     *
     * @param mixed $value
     * @param object $object
     * @return bool
     */
    public static function isClassConstantName($value, $object)
    {
        return in_array(strtoupper($value), array_keys(self::constants($object)));
    }
}
