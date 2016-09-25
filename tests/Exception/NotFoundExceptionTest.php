<?php

namespace PhowerTest\Container\Exception;

class NotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function testNotFoundExceptionExtendsRuntimeException()
    {
        $exception = new \Phower\Container\Exception\NotFoundException('SomeClass');
        $this->assertInstanceOf(\Phower\Container\Exception\RuntimeException::class, $exception);
    }

    public function testNotFoundExceptionImplementsNotFoundExceptionInterface()
    {
        $exception = new \Phower\Container\Exception\NotFoundException('SomeClass');
        $this->assertInstanceOf(\Interop\Container\Exception\NotFoundException::class, $exception);
    }

}
