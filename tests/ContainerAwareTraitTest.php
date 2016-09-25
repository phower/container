<?php

namespace PhowerTest\Container;

class ContainerAwareTraitTest extends \PHPUnit_Framework_TestCase
{

    public function testContainerAwareTraitSetsAndGetsContainer()
    {
        $trait = $this->getMockForTrait(\Phower\Container\ContainerAwareTrait::class);
        $container = $this->createMock(\Phower\Container\ContainerInterface::class);

        $this->assertNull($trait->getContainer());
        $trait->setContainer($container);
        $this->assertSame($container, $trait->getContainer());
    }

}
