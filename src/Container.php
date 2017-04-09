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
 * Cintainer
 *
 * @author Pedro Ferreira <pedro@phower.com>
 */
class Container implements ContainerInterface
{

    /**
     * @var array
     */
    private $names = [];

    /**
     * @var array
     */
    private $instances = [];

    /**
     * @var array
     */
    private $entries = [];

    /**
     * @var \Phower\Container\CompositeContainerInterface
     */
    private $delegator;

    /**
     * @var bool
     */
    protected $sharedByDefault;

    /**
     * @var bool
     */
    protected $allowOverride;

    /**
     * @var bool
     */
    protected $autoLock;

    /**
     * @var bool
     */
    protected $locked = false;

    /**
     * Valid values for entry type.
     *
     * @var array
     */
    protected static $entryTypes = [
        ContainerInterface::ENTRY_TYPE_CLASS,
        ContainerInterface::ENTRY_TYPE_FACTORY,
        ContainerInterface::ENTRY_TYPE_ABSTRACT_FACTORY,
        ContainerInterface::ENTRY_TYPE_ALIAS,
    ];

    /**
     * Class constructor
     *
     * @param CompositeContainerInterface $delegator
     * @param array $instances
     * @param bool $allowOverride
     * @param bool $autoLock
     * @param bool $sharedByDefault
     */
    public function __construct(CompositeContainerInterface $delegator = null, array $instances = [], $allowOverride = null, $autoLock = null, $sharedByDefault = null)
    {
        $this->delegator = $delegator;

        foreach ($instances as $name => $instance) {
            $this->set($name, $instance);
        }

        $this->allowOverride = null === $allowOverride ? self::DEFAULT_ALLOW_OVERRIDE : (bool) $allowOverride;
        $this->autoLock = null === $autoLock ? self::DEFAULT_AUTO_LOCK : (bool) $autoLock;
        $this->sharedByDefault = null === $sharedByDefault ? self::DEFAULT_SHARED_BY_DEFAULT : (bool) $sharedByDefault;
    }

    /**
     * Set delegator
     *
     * @param \Phower\Container\CompositeContainerInterface $delegator
     * @return \Phower\Container\Container
     * @throws Exception\LockedContainerException
     * @throws Exception\NotAllowedException
     */
    public function setDelegator(CompositeContainerInterface $delegator)
    {
        if ($this->locked) {
            throw new Exception\LockedContainerException();
        }

        if ($this->delegator && !$this->allowOverride) {
            throw new Exception\NotAllowedException();
        }

        $this->delegator = $delegator;

        return $this;
    }

    /**
     * Get delegator
     *
     * @return \Phower\Container\CompositeContainerInterface
     */
    public function getDelegator()
    {
        return $this->delegator;
    }

    /**
     * Set shared by default
     *
     * @param bool $sharedByDefault
     * @return \Phower\Container\Container
     * @throws Exception\LockedContainerException
     */
    public function setSharedByDefault($sharedByDefault)
    {
        if ($this->locked) {
            throw new Exception\LockedContainerException();
        }

        $this->sharedByDefault = (bool) $sharedByDefault;

        return $this;
    }

    /**
     * Is shared by default
     *
     * @return bool
     */
    public function isSharedByDefault()
    {
        return $this->sharedByDefault;
    }

    /**
     * Set allow override
     *
     * @param bool $allowOverride
     * @return \Phower\Container\Container
     * @throws Exception\LockedContainerException
     */
    public function setAllowOverride($allowOverride)
    {
        if ($this->locked) {
            throw new Exception\LockedContainerException();
        }

        $this->allowOverride = (bool) $allowOverride;

        return $this;
    }

    /**
     * Allow override
     *
     * @return bool
     */
    public function allowsOverride()
    {
        return $this->allowOverride;
    }

    /**
     * Lock container
     *
     * @return \Phower\Container\Container
     */
    public function lock()
    {
        $this->locked = true;

        return $this;
    }

    /**
     * Unlock container
     *
     * @return \Phower\Container\Container
     */
    public function unlock()
    {
        $this->locked = false;

        return $this;
    }

    /**
     * Is locked
     *
     * @return bool
     */
    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * Set auto lock
     *
     * @param bool $autoLock
     * @return \Phower\Container\Container
     * @throws Exception\LockedContainerException
     */
    public function setAutoLock($autoLock)
    {
        if ($this->locked) {
            throw new Exception\LockedContainerException();
        }

        $this->autoLock = (bool) $autoLock;

        return $this;
    }

    /**
     * Autolock
     *
     * @return bool
     */
    public function autoLock()
    {
        return $this->autoLock;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $name Identifier of the entry to look for.
     * @return mixed Entry.
     * @throws NotFoundException  No entry was found for this identifier.
     * @throws ContainerException Error while retrieving the entry.
     */
    public function get($name)
    {
        if ($this->autoLock && !$this->locked) {
            $this->locked = true;
        }

        if (!$this->has($name)) {
            throw new Exception\NotFoundException($name);
        }

        $normalized = $this->normalizeName($name);
        $entry = isset($this->entries[$this->names[$normalized]]) ?
                $this->entries[$this->names[$normalized]] : null;

        if (($entry === null || $entry['shared']) && isset($this->instances[$this->names[$normalized]])) {
            return $this->instances[$this->names[$normalized]];
        }

        $container = $this->delegator ?: $this;

        switch ($entry['type']) {
            case self::ENTRY_TYPE_CLASS:
                $class = $entry['entry'];
                $instance = new $class();

                break;
            case self::ENTRY_TYPE_FACTORY:
                if (is_string($entry['entry'])) {
                    $factory = $entry['entry'];
                    $entry['entry'] = new $factory();
                }

                $factory = $entry['entry'];

                if ($factory instanceof FactoryInterface) {
                    $instance = $factory->create($container);
                } else {
                    $instance = call_user_func_array($factory, [$container]);
                }

                break;
            case self::ENTRY_TYPE_ABSTRACT_FACTORY:
                if (is_string($entry['entry'])) {
                    $factory = $entry['entry'];
                    $entry['entry'] = new $factory();
                }

                /* @var $factory \Phower\Container\AbstractFactoryInterface */
                $factory = $entry['entry'];

                $instance = $factory->create($container, $name);

                break;
            case self::ENTRY_TYPE_ALIAS:
                $instance = $this->get($entry['entry']);

                break;
        }

        if (is_object($instance) && $instance instanceof ContainerAwareInterface) {
            $instance->setContainer($container);
        }

        if ($entry['shared']) {
            $this->instances[$name] = $instance;
        }

        return $instance;
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * @param string $name Identifier of the entry to look for.
     * @return bool
     */
    public function has($name)
    {
        if (isset($this->names[$name])) {
            return true;
        }

        $normalized = $this->normalizeName($name);

        if (isset($this->names[$normalized])) {
            return true;
        }

        foreach ($this->entries as $key => $entry) {
            if ($entry['type'] === self::ENTRY_TYPE_ABSTRACT_FACTORY && $this->hasAbstract($name, $key, $entry)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check whether an abstract factory can create a given name.
     *
     * @param string $name
     * @param string $key
     * @param array $entry
     * @return boolean
     */
    protected function hasAbstract($name, $key, array $entry)
    {
        if (is_string($entry['entry'])) {
            $factory = $entry['entry'];
            $this->entries[$key]['entry'] = $entry['entry'] = new $factory();
        }

        /* @var $factory \Phower\Container\AbstractFactoryInterface */
        $factory = $entry['entry'];
        $container = $this->delegator ?: $this;

        if ($factory->canCreate($container, $name)) {
            $normalized = $this->normalizeName($name);
            $this->names[$normalized] = $name;
            $this->entries[$name] = $entry;
            return true;
        }

        return false;
    }

    /**
     * Set instance
     *
     * @param string $name
     * @param mixed $instance
     * @throws Exception\RuntimeException
     * @throws Exception\InvalidArgumentException
     * @return \Phower\Container\Container
     */
    public function set($name, $instance)
    {
        $this->checkLockAndValidateName($name);

        $has = $this->has($name);

        if ($has && !$this->allowOverride) {
            throw new Exception\NotAllowedException();
        }

        if ($has) {
            $this->remove($name);
        }

        $normalized = $this->normalizeName($name);
        $this->names[$normalized] = $name;
        $this->instances[$name] = $instance;

        return $this;
    }

    /**
     * Remove
     *
     * @param string $name
     * @return \Phower\Container\Container
     * @throws Exception\RuntimeException
     * @throws Exception\InvalidArgumentException
     */
    public function remove($name)
    {
        $this->checkLockAndValidateName($name);

        $normalized = $this->normalizeName($name);
        $has = $this->has($normalized);

        if ($has) {
            if (!$this->allowOverride) {
                throw new Exception\NotAllowedException();
            }

            unset($this->entries[$this->names[$normalized]]);
            unset($this->names[$normalized]);
        }

        return $this;
    }

    /**
     * Add entry
     *
     * @param string $name
     * @param string|callable|FactoryInterface|AbstractFactoryInterface $entry
     * @param int $type
     * @param bool|null $shared
     * @return \Phower\Container\Container
     * @throws Exception\RuntimeException
     * @throws Exception\InvalidArgumentException
     */
    public function add($name, $entry, $type = self::ENTRY_TYPE_CLASS, $shared = null)
    {
        $this->checkLockAndValidateName($name);

        $normalized = $this->normalizeName($name);
        $isShared = $shared === null ? $this->sharedByDefault : (bool) $shared;

        switch ($type) {
            case self::ENTRY_TYPE_CLASS:
                $this->validateClassEntry($entry, $name, $isShared);
                if ($isShared && is_object($entry)) {
                    $this->instances[$name] = $entry;
                }
                break;
            case self::ENTRY_TYPE_FACTORY:
                $this->validateFactoryEntry($entry);
                break;
            case self::ENTRY_TYPE_ABSTRACT_FACTORY:
                $this->validateAbstractFactoryEntry($entry);
                break;
            case self::ENTRY_TYPE_ALIAS:
                $this->validateAliasEntry($entry);
                $entry = $this->normalizeName($entry);
                break;
            default:
                $types = implode(', ', self::$entryTypes);
                $message = sprintf('Invalid argument "type" in "%s"; please use one of "%s".', __METHOD__, $types);
                throw new Exception\InvalidArgumentException($message);
        }

        $this->remove($name);

        if ($type !== self::ENTRY_TYPE_ABSTRACT_FACTORY) {
            $this->names[$normalized] = $name;
        }

        $this->entries[$name] = [
            'type' => $type,
            'shared' => $isShared,
            'entry' => $entry,
        ];

        return $this;
    }

    /**
     * Validate class entry.
     *
     * @param string|object $entry
     * @throws Exception\InvalidArgumentException
     * @throws Exception\ClassNotFoundException
     */
    protected function validateClassEntry($entry)
    {
        if (!is_string($entry) && !is_object($entry)) {
            $type = gettype($entry);
            $message = sprintf('Argument "entry" in "%s" must be a string or an object; "%s" was given.', __METHOD__, $type);
            throw new Exception\InvalidArgumentException($message);
        }

        if (is_string($entry) && !class_exists($entry)) {
            throw new Exception\ClassNotFoundException($entry);
        }
    }

    /**
     * Validate factory entry.
     *
     * @param string|\Phower\Container\FactoryInterface $entry
     * @throws Exception\InvalidArgumentException
     * @throws Exception\ClassNotFoundException
     */
    protected function validateFactoryEntry($entry)
    {
        if (!is_string($entry) && !is_callable($entry) && !$entry instanceof FactoryInterface) {
            $type = is_object($entry) ? get_class($entry) : gettype($entry);
            $message = sprintf('Argument "entry" in "%s" must be a string, a callable or an instance '
                    . 'of "%s"; "%s" was given.', __METHOD__, FactoryInterface::class, $type);
            throw new Exception\InvalidArgumentException($message);
        }

        if (is_string($entry)) {
            if (!class_exists($entry)) {
                throw new Exception\ClassNotFoundException($entry);
            }

            if (!is_subclass_of($entry, FactoryInterface::class)) {
                $message = sprintf('Entry "%s" in "%s" is expected to be an instance of "%s".', $entry, __METHOD__, FactoryInterface::class);
                throw new Exception\InvalidArgumentException($message);
            }
        }
    }

    /**
     * Validate abastract factory entry.
     *
     * @param string|\Phower\Container\AbstractFactoryInterface $entry
     * @throws Exception\InvalidArgumentException
     * @throws Exception\ClassNotFoundException
     */
    protected function validateAbstractFactoryEntry($entry)
    {
        if (!is_string($entry) && !$entry instanceof AbstractFactoryInterface) {
            $type = is_object($entry) ? get_class($entry) : gettype($entry);
            $message = sprintf('Argument "entry" in "%s" must be a string or an instance '
                    . 'of "%s"; "%s" was given.', __METHOD__, AbstractFactoryInterface::class, $type);
            throw new Exception\InvalidArgumentException($message);
        }

        if (is_string($entry)) {
            if (!class_exists($entry)) {
                throw new Exception\ClassNotFoundException($entry);
            }

            if (!is_subclass_of($entry, AbstractFactoryInterface::class)) {
                $message = sprintf('Entry "%s" in "%s" is expected to be an instance of "%s".', $entry, __METHOD__, AbstractFactoryInterface::class);
                throw new Exception\InvalidArgumentException($message);
            }
        }
    }

    /**
     * Validate alias entry.
     *
     * @param string $entry
     * @throws Exception\InvalidArgumentException
     * @throws Exception\NotFoundException
     */
    protected function validateAliasEntry($entry)
    {
        if (!is_string($entry)) {
            $type = is_object($entry) ? get_class($entry) : gettype($entry);
            $message = sprintf('Argument "entry" in "%s" must be a string; "%s" was given.', __METHOD__, $type);
            throw new Exception\InvalidArgumentException($message);
        }

        if (!$this->has($entry)) {
            throw new Exception\NotFoundException($entry);
        }
    }

    /**
     * Check lock and validate name.
     *
     * @param string $name
     * @throws Exception\LockedContainerException
     * @throws Exception\InvalidNameException
     */
    protected function checkLockAndValidateName($name)
    {
        if ($this->locked) {
            throw new Exception\LockedContainerException();
        }

        if (!is_string($name) || trim($name) === '') {
            throw new Exception\InvalidNameException($name);
        }
    }

    /**
     * Add class
     *
     * @param string $name
     * @param string $class
     * @param bool|null $shared
     * @return \Phower\Container\Container
     */
    public function addClass($name, $class, $shared = null)
    {
        return $this->add($name, $class, self::ENTRY_TYPE_CLASS, $shared);
    }

    /**
     * Add factory
     *
     * @param string $name
     * @param string|callable|FactoryInterface $factory
     * @param bool|null $shared
     * @return \Phower\Container\Container
     */
    public function addFactory($name, $factory, $shared = null)
    {
        return $this->add($name, $factory, self::ENTRY_TYPE_FACTORY, $shared);
    }

    /**
     * Add abstract factory
     *
     * @param string $name
     * @param string|AbstractFactoryInterface $abstractFactory
     * @param bool|null $shared
     * @return \Phower\Container\Container
     */
    public function addAbstractFactory($name, $abstractFactory, $shared = null)
    {
        return $this->add($name, $abstractFactory, self::ENTRY_TYPE_ABSTRACT_FACTORY, $shared);
    }

    /**
     * Add alias
     *
     * @param string $name
     * @param string $alias
     * @param bool|null $shared
     * @return \Phower\Container\Container
     */
    public function addAlias($name, $alias, $shared = null)
    {
        return $this->add($name, $alias, self::ENTRY_TYPE_ALIAS, $shared);
    }

    /**
     * Get names
     *
     * @return array
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * Normalize name
     *
     * @param string $name
     * @return string
     */
    public function normalizeName($name)
    {
        return strtolower(preg_replace("/[^a-zA-Z0-9]+/", '', $name));
    }
}
