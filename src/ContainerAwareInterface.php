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
 * Container aware interface
 *
 * @author Pedro Ferreira <pedro@phower.com>
 */
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
