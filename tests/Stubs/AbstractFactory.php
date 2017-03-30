<?php

namespace PhowerTest\Container\Stubs;

use Phower\Container\AbstractFactoryInterface;
use Interop\Container\ContainerInterface;

class AbstractFactory implements AbstractFactoryInterface
{

    public function canCreate(ContainerInterface $container, $name)
    {
        return strlen($name) > 4 && substr($name, -4) === 'Test';
    }

    public function create(ContainerInterface $container, $name)
    {
        $name = substr($name, 0, -4);
        return new Dummy($name);
    }
}
