<?php

namespace PhowerTest\Container\Exception;

class ClassNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function testClassNotFoundExceptionExtendsInvalidArgumentException()
    {
        $exception = new \Phower\Container\Exception\ClassNotFoundException('SomeClass');
        $this->assertInstanceOf(\Phower\Container\Exception\InvalidArgumentException::class, $exception);
    }

}
