<?php

namespace PhowerTest\Container\Exception;

class NotAllowedExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function testNotAllowedExceptionExtendsRuntimeException()
    {
        $exception = new \Phower\Container\Exception\NotAllowedException('SomeClass');
        $this->assertInstanceOf(\Phower\Container\Exception\RuntimeException::class, $exception);
    }

}
