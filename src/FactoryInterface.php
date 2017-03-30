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

use Psr\Container\ContainerInterface as PsrInterface;

/**
 * Factory interface
 *
 * @author Pedro Ferreira <pedro@phower.com>
 */
interface FactoryInterface
{

    /**
     * Create
     *
     * @param \Psr\Container\ContainerInterface $container
     * @return mixed
     */
    public function create(PsrInterface $container);
}
