<?php

namespace PhowerTest\Container\Exception;

class InvalidArgumentExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function testInvalidArgumentExceptionExtendsRootInvalidArgumentException()
    {
        $exception = new \Phower\Container\Exception\InvalidArgumentException();
        $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
    }

    public function testInvalidArgumentExceptionImplementsContainerExceptionInterface()
    {
        $exception = new \Phower\Container\Exception\InvalidArgumentException();
        $this->assertInstanceOf(\Phower\Container\Exception\ContainerExceptionInterface::class, $exception);
    }

}
