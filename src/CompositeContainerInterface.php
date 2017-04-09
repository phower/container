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

use Psr\Container\ContainerInterface as PsrInterface;

/**
 * Composite container interface
 *
 * @author Pedro Ferreira <pedro@phower.com>
 */
interface CompositeContainerInterface extends PsrInterface
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
