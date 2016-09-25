<?php

namespace PhowerTest\Container;

class CompositeContainerTest extends \PHPUnit_Framework_TestCase
{

    public function testCompositeContainerImplementsCompositeContainerInterface()
    {
        $composite = $this->getMockBuilder(\Phower\Container\CompositeContainer::class)
                        ->disableOriginalConstructor()->getMock();
        $this->assertInstanceOf(\Phower\Container\CompositeContainerInterface::class, $composite);
    }

    public function testConstruct()
    {
        $composite = new \Phower\Container\CompositeContainer();
        $this->assertEmpty($composite->getContainers());

        $containers = [
            $this->createMock(\Phower\Container\ContainerInterface::class),
            $this->createMock(\Phower\Container\ContainerInterface::class),
            $this->createMock(\Phower\Container\ContainerInterface::class),
        ];

        $composite = new \Phower\Container\CompositeContainer($containers);
        $this->assertEquals($containers, $composite->getContainers());
    }

    public function testHas()
    {
        $composite = new \Phower\Container\CompositeContainer();
        $this->assertFalse($composite->has('dummy'));

        $container = new \Phower\Container\Container();
        $container->addClass('dummy', Stubs\Dummy::class);
        $composite->addContainer($container);
        $this->assertSame($composite, $container->getDelegator());
        $this->assertTrue($composite->has('dummy'));
    }

    public function testGet()
    {
        $composite = new \Phower\Container\CompositeContainer();
        $container = new \Phower\Container\Container();
        $container->addClass('dummy', Stubs\Dummy::class);
        $composite->addContainer($container);
        $this->assertInstanceOf(Stubs\Dummy::class, $composite->get('dummy'));
        
        $this->setExpectedException(\Phower\Container\Exception\NotFoundException::class);
        $composite->get('not-there');
    }

}
