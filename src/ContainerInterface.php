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

use Psr\Container\ContainerInterface as PsrContainerInterface;

/**
 * Container interface
 *
 * @author Pedro Ferreira <pedro@phower.com>
 */
interface ContainerInterface extends PsrContainerInterface
{

    /**
     * Set instance
     *
     * @param string $name
     * @param mixed $instance
     * @return \Phower\Container\ContainerInterface
     * @throws Exception\RuntimeException
     * @throws Exception\InvalidArgumentException
     */
    public function set($name, $instance);

    /**
     * Remove instance
     *
     * @param string $name
     * @return \Phower\Container\ContainerInterface
     * @throws Exception\RuntimeException
     * @throws Exception\InvalidArgumentException
     */
    public function remove($name);

    /**
     * Add entry
     *
     * @param string $name
     * @param string|callable|FactoryInterface|AbstractFactoryInterface $entry
     * @param int $type
     * @param bool|null $shared
     * @return \Phower\Container\ContainerInterface
     */
    public function add($name, $entry, $type, $shared = null);

    /**
     * Set delegator
     *
     * @param \Phower\Container\CompositeContainerInterface $delegator
     * @return \Phower\Container\Container
     * @throws Exception\LockedContainerException
     * @throws Exception\NotAllowedException
     */
    public function setDelegator(CompositeContainerInterface $delegator);

    /**
     * Get delegator
     *
     * @return \Phower\Container\CompositeContainerInterface|null
     */
    public function getDelegator();
}
