<?php

namespace b2r\Component\PropertyMethodDelegator;

use ReflectionMethod;
use b2r\Component\Exception\InvalidMethodException;

/**
 * Delegate method to property instance
 *
 * Using class MUST define protected static `$propertyMethodDelegator` property
 *
 * #### Features
 * - Delegate methods to target property
 * - Resolve method automatically
 * - Define method alias
 *
 * #### $propertyMethodDelegator structure
 * - Key: Property name
 * - Value: Method definitions
 *   - Key: Method name in **LOWER** case
 *   - Value: Delegate method name or bool
 *
 * ```php
 * class FooBar
 * {
 *     use PropertyMethodDelegator;
 *
 *     protected static $propertyMethodDelegator = [
 *         'foo' => [
 *             'execute' => true,  // `FooBar::execute()` invoke `FooBar::$foo->execute()`
 *         ],
 *         'bar' => [
 *             'run' => 'execute', // `FooBar::run()` invoke `FooBar::$bar->execute()`
 *         ],
 *     ];
 *
 *     public function __construct()
 *     {
 *         $this->foo = new Foo();
 *         $this->bar = new Bar();
 *     }
 * }
 * ```
 */
trait PropertyMethodDelegator
{
    public function __call($name, $arguments)
    {
        $method = $this->resolveDelegateMethod($name);
        if ($method) {
            return call_user_func_array($method, $arguments);
        }
        throw new InvalidMethodException($this, $name);
    }

    /**
     * Resolve delegate method
     *
     * @param  string $name Method name
     * @return array|false [object $instance, string $method] or false
     */
    public function resolveDelegateMethod($name)
    {
        $key = strtolower($name);
        $defs = static::$propertyMethodDelegator;

        // From definition
        foreach ($defs as $prop => $methods) {
            if (array_key_exists($key, $methods)) {
                $method = $methods[$key];
                if ($method === true) {
                    $method = $name;
                } elseif ($method === false) {
                    continue;
                }
                return [$this->$prop, $method];
            }
        }

        // Auto resolving
        foreach ($defs as $prop => $methods) {
            if (array_key_exists($key, $methods)) {
                continue;
            }
            $instance = $this->$prop;
            if (method_exists($instance, $name) && (new ReflectionMethod($instance, $name))->isPublic()) {
                static::$propertyMethodDelegator[$prop][$key] = $name;
                return [$instance, $name];
            }
        }

        return false;
    }

    public static function getPropertyMethodDelegatorInfo(): array
    {
        return static::$propertyMethodDelegator;
    }
}
