<?php

/**
 * Phower Container
 *
 * @version 0.0.0
 * @link https://github.com/phower/container Public Git repository
 * @copyright (c) 2015-2016, Pedro Ferreira <https://phower.com>
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Phower\Container;

use Interop\Container\ContainerInterface as InteropInterface;

/**
 * Abstract factory interface
 *
 * @author Pedro Ferreira <pedro@phower.com>
 */
interface AbstractFactoryInterface
{

    /**
     * Can create
     *
     * @param \Interop\Container\ContainerInterface $container
     * @param string $name
     * @return bool
     */
    public function canCreate(InteropInterface $container, $name);

    /**
     * Create
     *
     * @param \Interop\Container\ContainerInterface $container
     * @param string $name
     * @return mixed
     */
    public function create(InteropInterface $container, $name);
}
