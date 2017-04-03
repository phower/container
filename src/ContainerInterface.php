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
     * Entry types
     */
    const ENTRY_TYPE_CLASS = 1;
    const ENTRY_TYPE_FACTORY = 2;
    const ENTRY_TYPE_ABSTRACT_FACTORY = 3;
    const ENTRY_TYPE_ALIAS = 4;

    /**
     * Config names
     */
    const CONFIG_SHARED_BY_DEFAULT = 'shared_by_default';
    const CONFIG_ALLOW_OVERRIDE = 'allow_override';
    const CONFIG_AUTO_LOCK = 'auto_lock';
    const CONFIG_ENTRIES = 'entries';
    const CONFIG_ENTRY_TYPE = 'type';
    const CONFIG_ENTRY_NAME = 'name';
    const CONFIG_ENTRY_VALUE = 'value';
    const CONFIG_ENTRY_SHARED = 'shared';
    const CONFIG_CLASSES = 'classes';
    const CONFIG_FACTORIES = 'factories';
    const CONFIG_ABSTRACT_FACTORIES = 'abstract_factories';
    const CONFIG_ALIASES = 'aliases';

    /**
     * Default values
     */
    const DEFAULT_ALLOW_OVERRIDE = false;
    const DEFAULT_AUTO_LOCK = true;
    const DEFAULT_SHARED_BY_DEFAULT = true;

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
