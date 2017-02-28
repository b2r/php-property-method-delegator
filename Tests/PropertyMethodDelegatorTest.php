<?php

namespace b2r\Component\PropertyMethodDelegator;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/FooBar.php';

use b2r\Component\Exception\InvalidMethodException;
use b2r\Component\PropertyMethodDelegator\Test\ {
    Foo,
    Bar,
    Baz,
    FooBar,
    FooBarBaz,
    BarFoo
};

class PropertyMethodDelegatorTest extends \PHPUnit\Framework\TestCase
{
    public function is()
    {
        $args = func_get_args();
        if (count($args) === 2) {
            $this->assertEquals($args[0], $args[1]);
        } else {
            $this->assertTrue($args[0]);
        }
    }

    public function testFooBar()
    {
        $o = new FooBar();
        $this->is(Foo::class . '::execute', $o->execute());
        $this->is(Foo::class . '::doFoo', $o->foo());
        $this->is(Foo::class . '::hello', $o->hello());

        $this->is(Bar::class . '::run', $o->run());
        $this->is(Bar::class . '::doBar', $o->bar());

        $this->is(false, $o->resolveDelegateMethod('protectedMethod'));
        $this->is(false, $o->resolveDelegateMethod('publicHidden'));

        $this->assertTrue(is_array($o->getPropertyMethodDelegatorInfo()));
    }

    public function testBarFoo()
    {
        $o = new BarFoo();
        $this->is(Bar::class . '::execute', $o->execute());
        $this->is(Bar::class . '::run', $o->run());
        $this->is(Bar::class . '::doBar', $o->doBar());
        $this->is(Bar::class . '::hello', $o->hello());
        $this->is(Bar::class . '::publicHidden', $o->publicHidden());
        $this->is(Foo::class . '::doFoo', $o->doFoo());
    }

    public function testFooBarBaz()
    {
        $o = new FooBarBaz();
        $this->is($o instanceof FooBarBaz);
        $this->is(Foo::class . '::doFoo', $o->doFoo());
        $this->is(Bar::class . '::doBar', $o->doBar());
        $this->is(Baz::class . '::doBaz', $o->doBaz());
    }

    /**
     * @expectedException \b2r\Component\Exception\InvalidMethodException
     */
    public function testException()
    {
        $o = new FooBar();
        $o->publicHidden();
    }
}
