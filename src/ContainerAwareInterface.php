<?php

namespace Phower\Container;

interface ContainerAwareInterface
{

    /**
     * Set container
     *
     * @param \Phower\Container\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container);

    /**
     * Get container
     * 
     * @return \Phower\Container\ContainerInterface
     */
    public function getContainer();
}
