<?php

namespace PhowerTest\Container\Exception;

class RuntimeExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function testRuntimeExceptionExtendsRootRuntimeException()
    {
        $exception = new \Phower\Container\Exception\RuntimeException();
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testRuntimeExceptionImplementsContainerExceptionInterface()
    {
        $exception = new \Phower\Container\Exception\RuntimeException();
        $this->assertInstanceOf(\Phower\Container\Exception\ContainerExceptionInterface::class, $exception);
    }

}
