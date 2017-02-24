b2rPHP: PropertyMethodDelegator
===============================

Delegate method to property instance

- [CHANGELOG](CHANGELOG.md)

## Features
- Delegate methods to target property
- Resolve method automatically
- Define method alias

## $propertyMethodDelegator structure
- Key: `strign` Property name
- Value: `array` Method definitions
  - Key: `string` Method name in **LOWER** case
  - Value: `string|bool` Delegate target method name or bool
    - `true`: Use same name
    - `false`: DO NOT resolve method if target has public metdhod

## Usage

### Simple
```
use b2r\Component\PropertyMethodDelegator\PropertyMethodDelegator;

class ArrayObjectWrapper
{
    use PropertyMethodDelegator;

    protected static $propertyMethodDelegator = [
        'arrayObject' => [],
    ];

    private $arrayObject;

    public function __construct()
    {
        $this->arrayObject = new ArrayObject();
    }
}

$a = new ArrayObjectWrapper();
$a->append(1);
var_dump($a->getArrayCopy()); #=>[1]
```

### Mixed
```php
use b2r\Component\PropertyMethodDelegator\PropertyMethodDelegator;

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

class FooBar
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


$foobar = new FooBar();

echo $foobar->execute(),"\n"; #=>'Foo::execute'
echo $foobar->run(),"\n"; #=>'Bar::run'
echo $foobar->foo(),"\n"; #=>'Foo::doFoo'
echo $foobar->bar(),"\n"; #=>'Bar::doBar'
echo $foobar->doFoo(),"\n"; #=>'Foo::doFoo' Automatically resolved
echo $foobar->doBar(),"\n"; #=>'Bar::doBar' Automatically resolved
echo $foobar->hello(),"\n"; #=>'Foo::hello' Automatically resolved(foo is first)

// `protectedMethod` is protected, cannot resolve delegate method
var_dump($foobar->resolveDelegateMethod('protectedMethod')); #=> false

// `publicHidden` is hidden both foo and bar, cannot resolve delegate method
var_dump($foobar->resolveDelegateMethod('publicHidden')); #=> false

#------------------------------------------------------------

/**
 * Change delegate method resolving order: bar, foo
 */
class BarFoo extends FooBar
{
    protected static $propertyMethodDelegator = [
        'bar' => [],
        'foo' => [],
    ];
}

$barfoo = new BarFoo();
echo $barfoo->hello(),"\n"; #=>'Bar::hello' Automatically resolved(bar is first)
```
