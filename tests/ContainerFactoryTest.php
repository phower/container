<?php

namespace PhowerTest\Container;

use Phower\Container\Container;
use Phower\Container\ContainerFactory;
use Phower\Container\ContainerInterface;

class ContainerFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider configProvider
     */
    public function testFactoryCanCreateContainerFromConfig($config)
    {
        /* @var $container \Phower\Container\Container */
        $container = ContainerFactory::create($config);

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

        $this->assertInstanceOf(ContainerInterface::class, $container);
        $this->assertEquals($sharedByDefault, $container->isSharedByDefault());
        $this->assertEquals($allowOverride, $container->allowsOverride());
        $this->assertEquals($autoLock, $container->autoLock());

        foreach ($entries as $entry) {
            if ($entry[Container::CONFIG_ENTRY_TYPE] === ContainerInterface::ENTRY_TYPE_ABSTRACT_FACTORY) {
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
                            Container::CONFIG_ENTRY_TYPE => ContainerInterface::ENTRY_TYPE_CLASS,
                        ],
                        [
                            Container::CONFIG_ENTRY_NAME => Stubs\Factory::class,
                            Container::CONFIG_ENTRY_VALUE => Stubs\Factory::class,
                            Container::CONFIG_ENTRY_TYPE => ContainerInterface::ENTRY_TYPE_FACTORY,
                        ],
                        [
                            Container::CONFIG_ENTRY_NAME => Stubs\AbstractFactory::class,
                            Container::CONFIG_ENTRY_VALUE => Stubs\AbstractFactory::class,
                            Container::CONFIG_ENTRY_TYPE => ContainerInterface::ENTRY_TYPE_ABSTRACT_FACTORY,
                        ],
                        [
                            Container::CONFIG_ENTRY_NAME => 'dummy',
                            Container::CONFIG_ENTRY_VALUE => Stubs\Dummy::class,
                            Container::CONFIG_ENTRY_TYPE => ContainerInterface::ENTRY_TYPE_ALIAS,
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
        $this->expectException(\Phower\Container\Exception\InvalidArgumentException::class);
        ContainerFactory::create([Container::CONFIG_ENTRIES => [123]]);
    }
}
