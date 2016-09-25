<?php

namespace PhowerTest\Container\Exception;

class InvalidNameExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function testInvalidNameExceptionExtendsInvalidArgumentException()
    {
        $exception = new \Phower\Container\Exception\InvalidNameException('SomeClass');
        $this->assertInstanceOf(\Phower\Container\Exception\InvalidArgumentException::class, $exception);
    }

}
