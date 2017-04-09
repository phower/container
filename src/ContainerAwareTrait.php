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
 * Container aware trait
 *
 * @author Pedro Ferreira <pedro@phower.com>
 */
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
