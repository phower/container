<?php

namespace Phower\Container;

use Interop\Container\ContainerInterface as InteropContainer;

interface FactoryInterface
{

    /**
     * Create
     *
     * @param \Interop\Container\ContainerInterface $container
     * @return mixed
     */
    public function create(InteropContainer $container);
}
