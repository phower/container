<?php

namespace Phower\Container;

class Container implements ContainerInterface
{

    /**
     * Entry types
     */
    const ENTRY_CLASS = 1;
    const ENTRY_FACTORY = 2;
    const ENTRY_ABSTRACT_FACTORY = 3;
    const ENTRY_ALIAS = 4;

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
     * @var array
     */
    protected $entryTypes = [
        self::ENTRY_CLASS,
        self::ENTRY_FACTORY,
        self::ENTRY_ABSTRACT_FACTORY,
        self::ENTRY_ALIAS,
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
    public function __construct(CompositeContainerInterface $delegator = null, array $instances = [], $allowOverride = false, $autoLock = true, $sharedByDefault = true)
    {
        if ($delegator) {
            $this->delegator = $delegator;
        }

        $this->allowOverride = $allowOverride;
        $this->autoLock = $autoLock;
        $this->sharedByDefault = $sharedByDefault;

        foreach ($instances as $name => $instance) {
            $this->set($name, $instance);
        }
    }

    /**
     * Static factory method to create a new container
     *
     * @param array $config
     * @return \Phower\Container\Container
     */
    public static function create(array $config = [])
    {
        $container = new static();

        if (isset($config[self::CONFIG_SHARED_BY_DEFAULT])) {
            $container->setSharedByDefault($config[self::CONFIG_SHARED_BY_DEFAULT]);
        }

        if (isset($config[self::CONFIG_ALLOW_OVERRIDE])) {
            $container->setAllowOverride($config[self::CONFIG_ALLOW_OVERRIDE]);
        }

        if (isset($config[self::CONFIG_AUTO_LOCK])) {
            $container->setAutoLock($config[self::CONFIG_AUTO_LOCK]);
        }

        $defaultShared = $container->isSharedByDefault();

        if (isset($config[self::CONFIG_ENTRIES]) && is_array($config[self::CONFIG_ENTRIES])) {
            $required = [
                self::CONFIG_ENTRY_TYPE,
                self::CONFIG_ENTRY_NAME,
                self::CONFIG_ENTRY_VALUE,
            ];

            foreach ($config[self::CONFIG_ENTRIES] as $entry) {
                foreach ($required as $key) {
                    if (!isset($entry[$key])) {
                        $message = sprintf('Missing required entry key "%s" in "%s".', $key, __METHOD__);
                        throw new Exception\InvalidArgumentException($message);
                    }
                }

                $shared = isset($entry[self::CONFIG_ENTRY_SHARED]) ? $entry[self::CONFIG_ENTRY_SHARED] : null;

                if ($shared === null) {
                    $shared = $defaultShared;
                }

                $container->add($entry[self::CONFIG_ENTRY_NAME], $entry[self::CONFIG_ENTRY_VALUE], $entry[self::CONFIG_ENTRY_TYPE], $shared);
            }
        }

        if (isset($config[self::CONFIG_CLASSES]) && is_array($config[self::CONFIG_CLASSES])) {
            foreach ($config[self::CONFIG_CLASSES] as $name => $class) {
                $container->addClass($name, $class);
            }
        }

        if (isset($config[self::CONFIG_FACTORIES]) && is_array($config[self::CONFIG_FACTORIES])) {
            foreach ($config[self::CONFIG_FACTORIES] as $name => $factory) {
                $container->addFactory($name, $factory);
            }
        }

        if (isset($config[self::CONFIG_ABSTRACT_FACTORIES]) && is_array($config[self::CONFIG_ABSTRACT_FACTORIES])) {
            foreach ($config[self::CONFIG_ABSTRACT_FACTORIES] as $name => $factory) {
                $container->addAbstractFactory($name, $factory);
            }
        }

        if (isset($config[self::CONFIG_ALIASES]) && is_array($config[self::CONFIG_ALIASES])) {
            foreach ($config[self::CONFIG_ALIASES] as $name => $alias) {
                $container->addAlias($name, $alias);
            }
        }

        return $container;
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

        $container = $this->delegator ? : $this;

        switch ($entry['type']) {
            case self::ENTRY_CLASS:
                $class = $entry['entry'];
                $instance = new $class();

                break;
            case self::ENTRY_FACTORY:
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
            case self::ENTRY_ABSTRACT_FACTORY:
                if (is_string($entry['entry'])) {
                    $factory = $entry['entry'];
                    $entry['entry'] = new $factory();
                }

                /* @var $factory \Phower\Container\AbstractFactoryInterface */
                $factory = $entry['entry'];

                $instance = $factory->create($container, $name);

                break;
            case self::ENTRY_ALIAS:
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
            if ($entry['type'] === self::ENTRY_ABSTRACT_FACTORY) {
                if (is_string($entry['entry'])) {
                    $factory = $entry['entry'];
                    $this->entries[$key]['entry'] = $entry['entry'] = new $factory();
                }

                /* @var $factory \Phower\Container\AbstractFactoryInterface */
                $factory = $entry['entry'];
                $container = $this->delegator ? : $this;

                if ($factory->canCreate($container, $name)) {
                    $this->names[$normalized] = $name;
                    $this->entries[$name] = $entry;
                    return true;
                }
            }
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
        if ($this->locked) {
            throw new Exception\LockedContainerException();
        }

        if (!is_string($name)) {
            throw new Exception\InvalidNameException($name);
        }

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
        if ($this->locked) {
            throw new Exception\LockedContainerException();
        }

        if (!is_string($name)) {
            throw new Exception\InvalidNameException($name);
        }

        $normalized = $this->normalizeName($name);

        unset($this->entries[$this->names[$normalized]]);
        unset($this->names[$normalized]);

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
    public function add($name, $entry, $type, $shared = null)
    {
        if ($this->locked) {
            throw new Exception\LockedContainerException();
        }

        if (!is_string($name) || trim($name) === '') {
            throw new Exception\InvalidNameException($name);
        }

        $normalized = $this->normalizeName($name);
        $has = $this->has($normalized);

        if ($has && !$this->allowOverride) {
            throw new Exception\NotAllowedException();
        }

        if ($has) {
            $this->remove($name);
        }

        $shared = $shared === null ? $this->sharedByDefault : (bool) $shared;

        switch ($type) {
            case self::ENTRY_CLASS:
                if (!is_string($entry) && !is_object($entry)) {
                    $type = gettype($entry);
                    $message = sprintf('Argument "entry" in "%s" must be a string or an object; "%s" was given.', __METHOD__, $type);
                    throw new Exception\InvalidArgumentException($message);
                }

                if (is_string($entry) && !class_exists($entry)) {
                    throw new Exception\ClassNotFoundException($entry);
                }

                if ($shared && is_object($entry)) {
                    $this->instances[$name] = $entry;
                }

                break;
            case self::ENTRY_FACTORY:
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

                break;
            case self::ENTRY_ABSTRACT_FACTORY:
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

                break;
            case self::ENTRY_ALIAS:
                if (!is_string($entry)) {
                    $type = is_object($entry) ? get_class($entry) : gettype($entry);
                    $message = sprintf('Argument "entry" in "%s" must be a string; "%s" was given.', __METHOD__, $type);
                    throw new Exception\InvalidArgumentException($message);
                }

                if (!$this->has($entry)) {
                    throw new Exception\NotFoundException($entry);
                }

                $entry = $this->normalizeName($entry);
                break;
            default:
                $types = implode(', ', $this->entryTypes);
                $message = sprintf('Invalid argument "type" in "%s"; please use one of "%s".', __METHOD__, $types);
                throw new Exception\InvalidArgumentException($message);
        }

        if ($type !== self::ENTRY_ABSTRACT_FACTORY) {
            $this->names[$normalized] = $name;
        }

        $this->entries[$name] = [
            'type' => $type,
            'shared' => $shared,
            'entry' => $entry,
        ];

        return $this;
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
        return $this->add($name, $class, self::ENTRY_CLASS, $shared);
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
        return $this->add($name, $factory, self::ENTRY_FACTORY, $shared);
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
        return $this->add($name, $abstractFactory, self::ENTRY_ABSTRACT_FACTORY, $shared);
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
        return $this->add($name, $alias, self::ENTRY_ALIAS, $shared);
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
