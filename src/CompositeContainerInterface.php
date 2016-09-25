<?php

namespace Phower\Container;

use Interop\Container\ContainerInterface as InteropContainer;

interface CompositeContainerInterface extends InteropContainer
{

    /**
     * Add container
     *
     * @param \Phower\Container\ContainerInterface $container
     * @return \Phower\Container\CompositeContainerInterface
     */
    public function addContainer(ContainerInterface $container);

    /**
     * Set containers
     *
     * @param array $containers
     * @return \Phower\Container\CompositeContainerInterface
     */
    public function setContainers(array $containers);

    /**
     * Get containers
     *
     * @return array
     */
    public function getContainers();
}
