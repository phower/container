<?php

namespace Phower\Container;

trait ContainerAwareTrait
{

    /**
     * @var \Phower\Container\ContainerInterface
     */
    protected $container;

    /**
     * Set container
     *
     * @param \Phower\Container\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get container
     * 
     * @return \Phower\Container\ContainerInterface|null
     */
    public function getContainer()
    {
        return $this->container;
    }

}
