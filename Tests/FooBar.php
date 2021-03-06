<?php

namespace b2r\Component\PropertyMethodDelegator\Test;

use b2r\Component\PropertyMethodDelegator\ {
    PropertyMethodDelegator,
    PropertyMethodDelegatorInterface
};

class Foo
{
    public function doFoo()
    {
        return __METHOD__;
    }

    public function execute()
    {
        return __METHOD__;
    }

    public function run()
    {
        return __METHOD__;
    }

    public function hello() {
        return __METHOD__;
    }

    public function publicHidden()
    {
        return __METHOD__;
    }

    protected function protectedMethod()
    {
        return __METHOD__;
    }
}

class Bar
{
    public function doBar()
    {
        return __METHOD__;
    }

    public function execute()
    {
        return __METHOD__;
    }

    public function run()
    {
        return __METHOD__;
    }

    public function hello() {
        return __METHOD__;
    }

    public function publicHidden()
    {
        return __METHOD__;
    }

    protected function protectedMethod()
    {
        return __METHOD__;
    }
}

class Baz
{
    public function doBaz()
    {
        return __METHOD__;
    }
}

class FooBar implements PropertyMethodDelegatorInterface
{
    use PropertyMethodDelegator;

    protected static $propertyMethodDelegator = [
        'foo' => [
            'execute' => true, // FooBar::execute invoke FooBar::$foo::execute
            'foo' => 'doFoo',  // FooBar::foo invoke FooBar::$foo::doFoo
            'publichidden' => false, // DO NOT invoke FooBar::$foo::publicHidden
        ],
        'bar' => [
            'run' => true, // FooBar::run invoke FooBar::$bar::run
            'bar' => 'doBar', // FooBar::bar invoke FooBar::$bar::doBar
            'publichidden' => false,  // DO NOT invoke FooBar::$bar::publicHidden
        ],
    ];

    protected $foo;
    protected $bar;

    public function __construct()
    {
        $this->foo = new Foo();
        $this->bar = new Bar();
    }
}

class BarFoo extends FooBar implements PropertyMethodDelegatorInterface
{
    protected static $propertyMethodDelegator = [
        'bar' => [],
        'foo' => [],
    ];
}

class FooBarBaz implements PropertyMethodDelegatorInterface
{
    use PropertyMethodDelegator;

    protected static $propertyMethodDelegator = [
        'foobar' => [],
        'baz' => [],
    ];

    protected $foobar;
    protected $baz;

    public function __construct()
    {
        $this->foobar = new FooBar();
        $this->baz = new Baz();
    }
}
