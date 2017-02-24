<?php

namespace b2r\Component\PropertyMethodDelegator;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/FooBar.php';

use b2r\Component\Exception\InvalidMethodException;
use b2r\Component\PropertyMethodDelegator\Test\ {
    Foo,
    Bar,
    FooBar,
    BarFoo
};

class PropertyMethodDelegatorTest extends \PHPUnit\Framework\TestCase
{
    public function is($expected, $actual)
    {
        $this->assertEquals($expected, $actual);
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

    /**
     * @expectedException \b2r\Component\Exception\InvalidMethodException
     */
    public function testException()
    {
        $o = new FooBar();
        $o->publicHidden();
    }
}
