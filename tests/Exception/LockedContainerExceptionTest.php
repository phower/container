<?php

namespace PhowerTest\Container\Exception;

class LockedContainerExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function testLockedContainerExceptionExtendsRuntimeException()
    {
        $exception = new \Phower\Container\Exception\LockedContainerException('SomeClass');
        $this->assertInstanceOf(\Phower\Container\Exception\RuntimeException::class, $exception);
    }
}
