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
 * Container factory interface
 *
 * @author Pedro Ferreira <pedro@phower.com>
 */
interface ContainerFactoryInterface
{

    /**
     * Create a new container
     *
     * @param array $config
     * @return \Phower\Container\Container
     */
    public static function create(array $config = []);
}
