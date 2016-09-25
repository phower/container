<?php

namespace PhowerTest\Container;

use Phower\Container\Container;

class ContainerTest extends \PHPUnit_Framework_TestCase
{

    public function testContainerImplementsContainerInterface()
    {
        $container = $this->getMockBuilder(Container::class)
                        ->disableOriginalConstructor()->getMock();
        $this->assertInstanceOf(\Phower\Container\ContainerInterface::class, $container);
        $this->assertInstanceOf(\Interop\Container\ContainerInterface::class, $container);
    }

    public function testConstruct()
    {
        $delegator = $this->createMock(\Phower\Container\CompositeContainerInterface::class);
        $instances = ['foo' => 'bar'];
        $allowOverride = false;
        $autoLock = true;
        $sharedByDefault = true;

        $container = new Container($delegator, $instances, $allowOverride, $autoLock, $sharedByDefault);

        $this->assertEquals($delegator, $container->getDelegator());
        $this->assertTrue($container->has('foo'));
        $this->assertEquals($allowOverride, $container->allowsOverride());
        $this->assertEquals($autoLock, $container->autoLock());
        $this->assertEquals($sharedByDefault, $container->isSharedByDefault());
    }

    public function testSetSharedByDefault()
    {
        $container = new Container();
        $container->setSharedByDefault(false);
        $this->assertFalse($container->isSharedByDefault());
    }

    public function testSetSharedByDefaultThrowsExceptionWhenContainerIsLocked()
    {
        $container = new Container();
        $container->lock();
        $this->setExpectedException(\Phower\Container\Exception\LockedContainerException::class);
        $container->setSharedByDefault(false);
    }

    public function testSetAllowOverride()
    {
        $container = new Container();
        $container->setAllowOverride(true);
        $this->assertTrue($container->allowsOverride());
    }

    public function testSetAllowOverrideThrowsExceptionWhenContainerIsLocked()
    {
        $container = new Container();
        $container->lock();
        $this->setExpectedException(\Phower\Container\Exception\LockedContainerException::class);
        $container->setAllowOverride(true);
    }

    public function testSetAutoLock()
    {
        $container = new Container();
        $container->setAutoLock(false);
        $this->assertFalse($container->autoLock());
    }

    public function testSetAutoLockThrowsExceptionWhenContainerIsLocked()
    {
        $container = new Container();
        $container->lock();
        $this->setExpectedException(\Phower\Container\Exception\LockedContainerException::class);
        $container->setAutoLock(false);
    }

    public function testLockAndUnlock()
    {
        $container = new Container();
        $container->lock();
        $this->assertTrue($container->isLocked());
        $container->unlock();
        $this->assertFalse($container->isLocked());
    }

    public function testSetCanSetInstanceWithName()
    {
        $container = new Container();

        $container->set('foo', 'bar');
        $this->assertTrue($container->has('foo'));
        $this->assertEquals('bar', $container->get('foo'));
    }

    public function testSetThrowsExceptionWhenContainerIsLocked()
    {
        $container = new Container();
        $container->lock();
        $this->setExpectedException(\Phower\Container\Exception\RuntimeException::class);
        $container->set('foo', 'bar');
    }

    public function testSetThrowsExceptionWhenNameIsNotString()
    {
        $container = new Container();
        $this->setExpectedException(\Phower\Container\Exception\InvalidArgumentException::class);
        $container->set(123, 'bar');
    }

    public function testSetThrowsExceptionWhenNameExistsAndContainerDoesNotAllowOverride()
    {
        $container = new Container();
        $container->setAllowOverride(true);
        $this->assertFalse($container->has('foo'));
        $container->set('foo', 'bar');
        $this->assertTrue($container->has('foo'));
        $container->set('foo', 'baz');
        $container->setAllowOverride(false);
        $this->setExpectedException(\Phower\Container\Exception\RuntimeException::class);
        $container->set('foo', 'boo');
    }

    public function testHasReturnsTrueWhenAnInstanceExists()
    {
        $container = new Container();
        $this->assertFalse($container->has('foo'));
        $this->assertFalse($container->has('Foo'));
        $container->set('foo', 'bar');
        $this->assertTrue($container->has('foo'));
        $this->assertTrue($container->has('Foo'));
    }

    public function testHasReturnsTrueWhenAnAbstractFactoryCanCreateInstance()
    {
        $container = new Container();
        $container->addAbstractFactory(\PhowerTest\Container\Stubs\AbstractFactory::class, \PhowerTest\Container\Stubs\AbstractFactory::class);
        $this->assertFalse($container->has('foo'));
        $this->assertTrue($container->has('FooTest'));
    }

    public function testAddClassAddsEntryWhichResolvesToAnyClass()
    {
        $container = new Container();
        $container->addClass('Stubs\Dummy', Stubs\Dummy::class);
        $this->assertTrue($container->has('Stubs\Dummy'));
    }

    public function testAddFactoryAddsEntryWhichResolvesToFactoryInterface()
    {
        $container = new Container();
        $container->addFactory('Stubs\Factory', Stubs\Factory::class);
        $this->assertTrue($container->has('Stubs\Factory'));
    }

    public function testAddAliasAddsEntryWhichResolvesToAnotherName()
    {
        $container = new Container();
        $container->set('foo', 'bar');
        $container->addAlias('baz', 'foo');
        $this->assertTrue($container->has('baz'));
        $this->assertTrue($container->has('foo'));
    }

    public function testAddThrowsExceptionWhenContainerIsLocked()
    {
        $container = new Container();
        $container->lock();
        $this->setExpectedException(\Phower\Container\Exception\RuntimeException::class);
        $container->add('dummy', Stubs\Dummy::class, Container::ENTRY_CLASS);
    }

    public function testAddThrowsExceptionWhenNameIsNotString()
    {
        $container = new Container();
        $this->setExpectedException(\Phower\Container\Exception\InvalidArgumentException::class);
        $container->add(123, Stubs\Dummy::class, Container::ENTRY_CLASS);
    }

    public function testAddThrowsExceptionWhenNameIsOREvaluatesToEmptyString()
    {
        $container = new Container();
        $this->setExpectedException(\Phower\Container\Exception\InvalidArgumentException::class);
        $container->add(" \n\t", Stubs\Dummy::class, Container::ENTRY_CLASS);
    }

    public function testAddThrowsExceptionWhenContainerHasNameAndDoesNotAllowOverride()
    {
        $container = new Container();
        $container->setAllowOverride(true);
        $container->add("dummy", Stubs\Dummy::class, Container::ENTRY_CLASS);
        $container->add("dummy", new Stubs\Dummy(), Container::ENTRY_CLASS);
        $container->setAllowOverride(false);
        $this->setExpectedException(\Phower\Container\Exception\RuntimeException::class);
        $container->add("dummy", Stubs\Dummy::class, Container::ENTRY_CLASS);
    }

    public function testAddThrowsExceptionWhenTypeIsClassAndEntryIsNotClassNameOrIsNotObjectInstance()
    {
        $container = new Container();
        $this->setExpectedException(\Phower\Container\Exception\InvalidArgumentException::class);
        $container->add("dummy", 123, Container::ENTRY_CLASS);
    }

    public function testAddThrowsExceptionWhenTypeIsClassAndEntryClassNameDoesNotExist()
    {
        $container = new Container();
        $this->setExpectedException(\Phower\Container\Exception\InvalidArgumentException::class);
        $container->add("dummy", 'NotAValidClass', Container::ENTRY_CLASS);
    }

    public function testAddThrowsExceptionWhenTypeIsFactoryAndEntryIsNotClassNameIsNotCallableOrIsNotObjectInstance()
    {
        $container = new Container();
        $container->add("factoryName", Stubs\Factory::class, Container::ENTRY_FACTORY);
        $container->add("factoryClosure", function($c) {
            return new Stubs\Dummy();
        }, Container::ENTRY_FACTORY);
        $container->add("factoryObject", new Stubs\Factory(), Container::ENTRY_FACTORY);
        $this->setExpectedException(\Phower\Container\Exception\InvalidArgumentException::class);
        $container->add("dummyNumber", 123, Container::ENTRY_FACTORY);
    }

    public function testAddThrowsExceptionWhenTypeIsFactoryAndEntryIsClassNameAndClassDoesNotExist()
    {
        $container = new Container();
        $this->setExpectedException(\Phower\Container\Exception\InvalidArgumentException::class);
        $container->add("factoryName", 'NotAValidClass', Container::ENTRY_FACTORY);
    }

    public function testAddThrowsExceptionWhenTypeIsFactoryAndEntryIsClassNameAndClassIsNotInstanceOfFactoryInterface()
    {
        $container = new Container();
        $this->setExpectedException(\Phower\Container\Exception\InvalidArgumentException::class);
        $container->add("factoryName", Stubs\Dummy::class, Container::ENTRY_FACTORY);
    }

    public function testAddThrowsExceptionWhenTypeIsAbstractFactoryAndEntryIsNotClassNameAndIsNotInstanceOfAbstractFactory()
    {
        $container = new Container();
        $this->setExpectedException(\Phower\Container\Exception\InvalidArgumentException::class);
        $container->add("factoryName", new Stubs\Dummy(), Container::ENTRY_ABSTRACT_FACTORY);
    }

    public function testAddThrowsExceptionWhenTypeIsAbstractFactoryAndEntryIsClassNameAndClassDoesNotExist()
    {
        $container = new Container();
        $this->setExpectedException(\Phower\Container\Exception\InvalidArgumentException::class);
        $container->add("factoryName", 'NotAValidClass', Container::ENTRY_ABSTRACT_FACTORY);
    }

    public function testAddThrowsExceptionWhenTypeIsAbstractFactoryAndEntryIsClassNameAndClassIsNotInstanceOfAbstractFactoryInterface()
    {
        $container = new Container();
        $this->setExpectedException(\Phower\Container\Exception\InvalidArgumentException::class);
        $container->add("factoryName", Stubs\Dummy::class, Container::ENTRY_ABSTRACT_FACTORY);
    }

    public function testAddThrowsExceptionWhenTypeIsAliasAndEntryIsNotString()
    {
        $container = new Container();
        $this->setExpectedException(\Phower\Container\Exception\InvalidArgumentException::class);
        $container->add("alias", new Stubs\Dummy(), Container::ENTRY_ALIAS);
    }

    public function testAddThrowsExceptionWhenTypeIsAliasAndEntryIsNotValidName()
    {
        $container = new Container();
        $this->setExpectedException(\Phower\Container\Exception\NotFoundException::class);
        $container->add("alias", 'unknown', Container::ENTRY_ALIAS);
    }

    public function testAddThrowsExceptionWhenTypeIsNotValid()
    {
        $container = new Container();
        $this->setExpectedException(\Phower\Container\Exception\InvalidArgumentException::class);
        $container->add("name", 'entry', 123);
    }

    public function testGetNamesReturnsArrayOfRegisteredNames()
    {
        $container = new Container();
        $names = [
            'dummy' => 'dummy',
            'stubsdummy' => 'Stubs\Dummy',
            'phowertestcontainerstubsdummy' => Stubs\Dummy::class,
        ];
        $container->addClass('dummy', Stubs\Dummy::class);
        $container->addClass('Stubs\Dummy', Stubs\Dummy::class);
        $container->addClass(Stubs\Dummy::class, Stubs\Dummy::class);
        $this->assertEquals($names, $container->getNames());
    }

    public function testRemoveThrowsExceptionWhenContainerIsLocked()
    {
        $container = new Container();
        $container->lock();
        $this->setExpectedException(\Phower\Container\Exception\RuntimeException::class);
        $container->remove('dummy');
    }

    public function testRemoveThrowsExceptionWhenNameIsNotString()
    {
        $container = new Container();
        $this->setExpectedException(\Phower\Container\Exception\InvalidArgumentException::class);
        $container->remove(123);
    }

    public function testGetThrowsExceptionWhenNameIsNotFound()
    {
        $container = new Container();
        $this->setExpectedException(\Phower\Container\Exception\NotFoundException::class);
        $container->get('dummy');
    }

    public function testGetCanCreateInstanceWhenEntryIsSharedAndInstanceDoesNotExist()
    {
        $container = new Container();
        $container->setAutoLock(false);

        $container->addClass('sharedClass', Stubs\Dummy::class, true);
        $instance = $container->get('sharedClass');
        $this->assertInstanceOf(Stubs\Dummy::class, $instance);
        $this->assertSame($instance, $container->get('sharedClass'));

        $container->addFactory('factoryEntry', Stubs\Factory::class, true);
        $instance = $container->get('factoryEntry');
        $this->assertInstanceOf(Stubs\Dummy::class, $instance);
        $this->assertSame($instance, $container->get('factoryEntry'));

        $closure = function($c) {
            return new Stubs\Dummy();
        };
        $container->addFactory('closureEntry', $closure, true);
        $instance = $container->get('closureEntry');
        $this->assertInstanceOf(Stubs\Dummy::class, $instance);
        $this->assertSame($instance, $container->get('closureEntry'));

        $callable = [Stubs\Dummy::class, 'create'];
        $container->addFactory('callableEntry', $callable, true);
        $instance = $container->get('callableEntry');
        $this->assertInstanceOf(Stubs\Dummy::class, $instance);
        $this->assertSame($instance, $container->get('callableEntry'));

        $container->addAbstractFactory('abstractFactoryEntry', Stubs\AbstractFactory::class, true);
        $instance = $container->get('SomeTest');
        $this->assertInstanceOf(Stubs\Dummy::class, $instance);
        $this->assertSame($instance, $container->get('SomeTest'));

        $container->addAlias('aliasToSomeTest', 'SomeTest', true);
        $instance = $container->get('aliasToSomeTest');
        $this->assertInstanceOf(Stubs\Dummy::class, $instance);
        $this->assertSame($instance, $container->get('aliasToSomeTest'));
    }

    public function testGetAlwaysCreateInstanceWhenEntryIsNotShared()
    {
        $container = new Container();
        $container->setAutoLock(false);

        $container->addClass('notSharedClass', Stubs\Dummy::class, false);
        $instance = $container->get('notSharedClass');
        $this->assertInstanceOf(Stubs\Dummy::class, $instance);
        $this->assertNotSame($instance, $container->get('notSharedClass'));

        $container->addFactory('factoryEntry', Stubs\Factory::class, false);
        $instance = $container->get('factoryEntry');
        $this->assertInstanceOf(Stubs\Dummy::class, $instance);
        $this->assertNotSame($instance, $container->get('factoryEntry'));

        $closure = function($c) {
            return new Stubs\Dummy();
        };
        $container->addFactory('closureEntry', $closure, false);
        $instance = $container->get('closureEntry');
        $this->assertInstanceOf(Stubs\Dummy::class, $instance);
        $this->assertNotSame($instance, $container->get('closureEntry'));

        $callable = [Stubs\Dummy::class, 'create'];
        $container->addFactory('callableEntry', $callable, false);
        $instance = $container->get('callableEntry');
        $this->assertInstanceOf(Stubs\Dummy::class, $instance);
        $this->assertNotSame($instance, $container->get('callableEntry'));

        $container->addAbstractFactory('abstractFactoryEntry', Stubs\AbstractFactory::class, false);
        $instance = $container->get('SomeTest');
        $this->assertInstanceOf(Stubs\Dummy::class, $instance);
        $this->assertNotSame($instance, $container->get('SomeTest'));

        $container->addAlias('aliasToSomeTest', 'SomeTest', false);
        $instance = $container->get('aliasToSomeTest');
        $this->assertInstanceOf(Stubs\Dummy::class, $instance);
        $this->assertNotSame($instance, $container->get('aliasToSomeTest'));
    }

    public function testSetDelegator()
    {
        $container = new Container();
        $delegator = $this->createMock(\Phower\Container\CompositeContainerInterface::class);
        $container->setDelegator($delegator);
        $this->assertSame($delegator, $container->getDelegator());
    }

    public function testSetDelegatorThrowsExceptionWhenContainerIsLocked()
    {
        $container = new Container();
        $delegator = $this->createMock(\Phower\Container\CompositeContainerInterface::class);
        $container->lock();
        $this->setExpectedException(\Phower\Container\Exception\LockedContainerException::class);
        $container->setDelegator($delegator);
    }

    public function testSetDelegatorThrowsExceptionWhenContainerDoesNotAllowOverride()
    {
        $delegator = $this->createMock(\Phower\Container\CompositeContainerInterface::class);
        $container = new Container($delegator);
        $container->setAllowOverride(false);
        $this->setExpectedException(\Phower\Container\Exception\NotAllowedException::class);
        $container->setDelegator($delegator);
    }

    public function testGetInjectsContainerWhenReturnedInstanceIsContainerAware()
    {
        $container = new Container();
        $container->addClass('withSimpleContainer', Stubs\ContainerAware::class);
        $instance = $container->get('withSimpleContainer');
        $this->assertInstanceOf(\Phower\Container\ContainerAwareInterface::class, $instance);
        $this->assertSame($container, $instance->getContainer());
    }

    /**
     * @dataProvider configProvider
     */
    public function testFactoryCanCreateContainerFromConfig($config)
    {
        /* @var $container \Phower\Container\Container */
        $container = Container::create($config);

        $sharedByDefault = isset($config[Container::CONFIG_SHARED_BY_DEFAULT]) ?
                (bool) $config[Container::CONFIG_SHARED_BY_DEFAULT] :
                $container->isSharedByDefault();
        $allowOverride = isset($config[Container::CONFIG_ALLOW_OVERRIDE]) ?
                (bool) $config[Container::CONFIG_ALLOW_OVERRIDE] :
                $container->allowsOverride();
        $autoLock = isset($config[Container::CONFIG_AUTO_LOCK]) ?
                (bool) $config[Container::CONFIG_AUTO_LOCK] :
                $container->autoLock();
        $entries = isset($config[Container::CONFIG_ENTRIES]) ?
                $config[Container::CONFIG_ENTRIES] : [];

        $this->assertInstanceOf(\Phower\Container\ContainerInterface::class, $container);
        $this->assertEquals($sharedByDefault, $container->isSharedByDefault());
        $this->assertEquals($allowOverride, $container->allowsOverride());
        $this->assertEquals($autoLock, $container->autoLock());

        foreach ($entries as $entry) {
            if ($entry[Container::CONFIG_ENTRY_TYPE] === Container::ENTRY_ABSTRACT_FACTORY) {
                $this->assertFalse($container->has($entry[Container::CONFIG_ENTRY_NAME]));
            } else {
                $this->assertTrue($container->has($entry[Container::CONFIG_ENTRY_NAME]));
            }
        }
    }

    public function configProvider()
    {
        return [
            [[]],
            [
                [
                    Container::CONFIG_SHARED_BY_DEFAULT => true,
                    Container::CONFIG_ALLOW_OVERRIDE => true,
                    Container::CONFIG_AUTO_LOCK => true,
                ]
            ],
            [
                [
                    Container::CONFIG_SHARED_BY_DEFAULT => false,
                    Container::CONFIG_ALLOW_OVERRIDE => false,
                    Container::CONFIG_AUTO_LOCK => false,
                ]
            ],
            [
                [
                    Container::CONFIG_ENTRIES => [],
                ]
            ],
            [
                [
                    Container::CONFIG_ENTRIES => [
                        [
                            Container::CONFIG_ENTRY_NAME => Stubs\Dummy::class,
                            Container::CONFIG_ENTRY_VALUE => Stubs\Dummy::class,
                            Container::CONFIG_ENTRY_TYPE => Container::ENTRY_CLASS,
                        ],
                        [
                            Container::CONFIG_ENTRY_NAME => Stubs\Factory::class,
                            Container::CONFIG_ENTRY_VALUE => Stubs\Factory::class,
                            Container::CONFIG_ENTRY_TYPE => Container::ENTRY_FACTORY,
                        ],
                        [
                            Container::CONFIG_ENTRY_NAME => Stubs\AbstractFactory::class,
                            Container::CONFIG_ENTRY_VALUE => Stubs\AbstractFactory::class,
                            Container::CONFIG_ENTRY_TYPE => Container::ENTRY_ABSTRACT_FACTORY,
                        ],
                        [
                            Container::CONFIG_ENTRY_NAME => 'dummy',
                            Container::CONFIG_ENTRY_VALUE => Stubs\Dummy::class,
                            Container::CONFIG_ENTRY_TYPE => Container::ENTRY_ALIAS,
                        ],
                    ],
                ]
            ],
            [
                [
                    Container::CONFIG_CLASSES => [
                        Stubs\Dummy::class => Stubs\Dummy::class,
                    ],
                    Container::CONFIG_FACTORIES => [
                        Stubs\Factory::class => Stubs\Factory::class,
                    ],
                    Container::CONFIG_ABSTRACT_FACTORIES => [
                        Stubs\AbstractFactory::class => Stubs\AbstractFactory::class,
                    ],
                    Container::CONFIG_ALIASES => [
                        'dummy' => Stubs\Dummy::class,
                    ],
                ]
            ],
        ];
    }

    public function testFactoryRaisesExceptionOnInvalidEntry()
    {
        $this->setExpectedException(\Phower\Container\Exception\InvalidArgumentException::class);
        Container::create([Container::CONFIG_ENTRIES => [123]]);
    }

}
