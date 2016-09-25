<?php

namespace Phower\Container;

use Interop\Container\ContainerInterface as InteropContainer;

interface AbstractFactoryInterface
{

    /**
     * Can create
     *
     * @param \Interop\Container\ContainerInterface $container
     * @param string $name
     * @return bool
     */
    public function canCreate(InteropContainer $container, $name);

    /**
     * Create
     *
     * @param \Interop\Container\ContainerInterface $container
     * @param string $name
     * @return mixed
     */
    public function create(InteropContainer $container, $name);
}
