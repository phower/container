Phower Container
================

Dependency Injection for PHP complaint with Container-Interop.

Requirements
------------

Phower Container requires:

-   [PHP 5.6](http://php.net/releases/5_6_0.php) or above; 
    version [7.0](http://php.net/releases/7_0_0.php) is recommended

Instalation
-----------

Add Phower Container to any PHP project using [Composer](https://getcomposer.org/):

```bash
composer require phower/container
```

Usage
=====

In software engineering, dependency injection is a software design pattern that 
implements inversion of control for resolving dependencies. 

A dependency is an object that can be used (a service). An injection is the passing 
of a dependency to a dependent object (a client) that would use it.

In order to solve the above we can use a container which knows how to resolve names
into instances. This way we can simply inject our container into our classes and then 
just ask it to provide he required dependencies.

### Instances

Creating and using a container is quite simple:

```php
// in a bootstrap create the container and set a dependency
use Phower\Container\Container;

$container = new Container();
$container->set('some', new SomeClass());
$container->set('another', new AnotherClass());

// later inside our class aware of the container
if ($container->has('some') {
    /* @var $some SomeClass */
    $some = $container->get('some');
}
```

However this is not very efficient in case you need to register hundreds or 
thousands of instances, which will take too much time and computer resources
each time the container goes to be initialized.

### Entries

A more efficient way is to simply add entries to the container which are then 
resolved to the right instance at runtime:

```php
// in a bootstrap create the container and set a dependency
use Phower\Container\Container;

$container = new Container();
$container->add('some', 'SomeClass');
$container->add('another', 'AnotherClass');

// later inside our class aware of the container
if ($container->has('some') {
    /* @var $some SomeClass */
    $some = $container->get('some');
}
```

### Factories

But what if our dependency requires arguments, which may also be retrieved from 
inside the container? The solution is to register a factory class instead of our
final class.

First let's create a factory class:

```php
use Phower\Container\FactoryInterface;
use Phower\Container\ContainerInterface;

class SomeFactory implments FactoryInterface
{
    public function create(ContainerInterface $container)
    {
        $arg1 = 123;
        $arg2 = $container->get('arg2');
        return new SomeClass($arg1, $arg2);
    }
}
```

> Note that your factories must always implement the [Phower\Container\FactoryInterface](src/FactoryInterface.php).

Then register the factory and the required argument on the container:

```php
// in a bootstrap create the container and set a dependency
use Phower\Container\Container;

$container = new Container();
$container->add('arg2', 'AnotherClass');
$container->addFactory('some', 'SomeFactory');

// later inside our class aware of the container
if ($container->has('some') {
    /* @var $some SomeClass */
    $some = $container->get('some');
}
```

### Abstract Factories

Another way to retrieve dependencies from a container is to use an abstract (dynamic) 
factory. This way you can instantiate dependencies base on a name patter or a namespace.

Let's say you have many classes under the namespace 'Some':

-   Some\OneClass
-   Some\OtherClass
-   Some\ThirdClass

Instead of registering them separatedly we just need a single abstract factory:

```php
use Phower\Container\AbstractFactoryInterface;
use Phower\Container\ContainerInterface;

class SomeAbstractFactory implments AbstractFactoryInterface
{
    public function canCreate(ContainerInterface $container, $name)
    {
        return substr($name, 0, 5) === 'Some\\';
    }

    public function create(ContainerInterface $container, $name)
    {
        return new $name();
    }
}
```

> Note that your abstract factories must always implement the 
> [Phower\Container\AbstractFactoryInterface](src/AbstractFactoryInterface.php).

Then register the abstract factory on the container:

```php
// in a bootstrap create the container and set a dependency
use Phower\Container\Container;

$container = new Container();
$container->addAbstractFactory('some', 'SomeAbstractFactory');

// later inside our class aware of the container

/* @var $one Some\OneClass */
$one = $container->get('Some\OneClass');

/* @var $other Some\OtherClass */
$other = $container->get('Some\OtherClass');

/* @var $third Some\ThirdClass */
$third = $container->get('Some\ThirdClass ');
```

### Aliases

Sometimes you need to have different names to the same entry on the container.
This can be achieved adding aliases instead of duplicating references:

```php
// in a bootstrap create the container and set a dependency
use Phower\Container\Container;

$container = new Container();
$container->add('some', 'SomeClass');
$container->addAlias('something', 'some');

// later inside our class aware of the container
if ($container->has('something') {
    /* @var $something SomeClass */
    $something = $container->get('something');
}
```

### Create container from config

Most of times it would be better to create the container from a (huge) configuration
array. This can be done using the static *create* method with an array of entries as
argument:

```php
// in a bootstrap create the container and set a dependency
use Phower\Container\Container;

$container = Container::create([
    'classes' => [
        'some' => 'SomeClass',
        // more classes ...
    ],
    'factories' => [
        'some-factory' => 'SomeFactory',
        // more factories ...
    ],
    'abstract_factories' => [
        'abstract-factory' => 'SomeAbstractFactory',
        // more abstract factories ...
    ],
    'aliases' => [
        'alis-to-some' => 'some',
        // more aliases ...
    ],
]);
```

Running Tests
-------------

Tests are available in a separated namespace and can run with [PHPUnit](http://phpunit.de/)
in the command line:

```bash
vendor/bin/phpunit
```

Coding Standards
----------------

Phower code is written under [PSR-2](http://www.php-fig.org/psr/psr-2/) coding style standard.
To enforce that [CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) tools are also 
provided and can run as:

```bash
vendor/bin/phpcs
```

Reporting Issues
----------------

In case you find issues with this code please open a ticket in Github Issues at
[https://github.com/phower/container/issues](https://github.com/phower/container/issues).

Contributors
------------

Open Source is made of contribuition. If you want to contribute to Phower please
follow these steps:

1.  Fork latest version into your own repository.
2.  Write your changes or additions and commit them.
3.  Follow PSR-2 coding style standard.
4.  Make sure you have unit tests with full coverage to your changes.
5.  Go to Github Pull Requests at [https://github.com/phower/container/pulls](https://github.com/phower/container/pulls)
    and create a new request.

Thank you!

Changes and Versioning
----------------------

All relevant changes on this code are logged in a separated [log](CHANGELOG.md) file.

Version numbers follow recommendations from [Semantic Versioning](http://semver.org/).

License
-------

Phower code is maintained under [The MIT License](https://opensource.org/licenses/MIT).