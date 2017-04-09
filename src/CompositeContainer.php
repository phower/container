<?php

/**
 * Phower Container
 *
 * @version 1.0.0
 * @link https://github.com/phower/container Public Git repository
 * @copyright (c) 2015-2016, Pedro Ferreira <https://phower.com>
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Phower\Container;

/**
 * Composite container
 *
 * @author Pedro Ferreira <pedro@phower.com>
 */
class CompositeContainer implements CompositeContainerInterface
{

    /**
     * @var array
     */
    private $containers;

    /**
     * Class constructor
     *
     * @param array $containers
     */
    public function __construct(array $containers = [])
    {
        $this->setContainers($containers);
    }

    /**
     * Add container
     *
     * @param \Phower\Container\ContainerInterface $container
     * @return \Phower\Container\CompositeContainer|null
     */
    public function addContainer(ContainerInterface $container)
    {
        $container->setDelegator($this);
        $this->containers[] = $container;
        return $this;
    }

    /**
     * Set containers
     *
     * @param array $containers
     * @return \Phower\Container\CompositeContainer
     */
    public function setContainers(array $containers)
    {
        $this->containers = [];

        foreach ($containers as $container) {
            $this->addContainer($container);
        }

        return $this;
    }

    /**
     * Get containers
     *
     * @return array
     */
    public function getContainers()
    {
        return $this->containers;
    }

    /**
     * Has
     *
     * @param string $name
     * @return boolean
     */
    public function has($name)
    {
        foreach ($this->containers as $container) {
            if ($container->has($name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get
     *
     * @param string $name
     * @return mixed
     * @throws Exception\NotFoundException
     */
    public function get($name)
    {
        foreach ($this->containers as $container) {
            if ($container->has($name)) {
                return $container->get($name);
            }
        }

        throw new Exception\NotFoundException($name);
    }
}
