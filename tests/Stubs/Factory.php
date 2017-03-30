<?php

namespace PhowerTest\Container\Stubs;

use Phower\Container\FactoryInterface;
use Interop\Container\ContainerInterface;

class Factory implements FactoryInterface
{

    public function create(ContainerInterface $container)
    {
        return new Dummy();
    }
}
