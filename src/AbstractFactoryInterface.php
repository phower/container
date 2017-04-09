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
 * Abstract factory interface
 *
 * @author Pedro Ferreira <pedro@phower.com>
 */
interface AbstractFactoryInterface
{

    /**
     * Can create
     *
     * @param \Psr\Container\ContainerInterface $container
     * @param string $name
     * @return bool
     */
    public function canCreate(PsrInterface $container, $name);

    /**
     * Create
     *
     * @param \Psr\Container\ContainerInterface $container
     * @param string $name
     * @return mixed
     */
    public function create(PsrInterface $container, $name);
}
