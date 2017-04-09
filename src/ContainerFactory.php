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
 * Cintainer factory
 *
 * @author Pedro Ferreira <pedro@phower.com>
 */
class ContainerFactory implements ContainerFactoryInterface
{

    /**
     * Required entry keys.
     *
     * @var array
     */
    protected static $requiredEntryKeys = [
        ContainerInterface::CONFIG_ENTRY_TYPE,
        ContainerInterface::CONFIG_ENTRY_NAME,
        ContainerInterface::CONFIG_ENTRY_VALUE,
    ];

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
     * Boolean setters.
     *
     * @var array
     */
    protected static $booleanSetters = [
        ContainerInterface::CONFIG_ALLOW_OVERRIDE => 'setAllowOverride',
        ContainerInterface::CONFIG_AUTO_LOCK => 'setAutoLock',
        ContainerInterface::CONFIG_SHARED_BY_DEFAULT => 'setSharedByDefault',
    ];

    /**
     * Array setters.
     *
     * @var array
     */
    protected static $arraySetters = [
        ContainerInterface::CONFIG_CLASSES => 'addClass',
        ContainerInterface::CONFIG_FACTORIES => 'addFactory',
        ContainerInterface::CONFIG_ABSTRACT_FACTORIES => 'addAbstractFactory',
        ContainerInterface::CONFIG_ALIASES => 'addAlias',
    ];

    /**
     * Static factory method to create a new container
     *
     * @param array $config
     * @return \Phower\Container\Container
     */
    public static function create(array $config = [])
    {
        $container = new Container();

        foreach (self::$booleanSetters as $key => $method) {
            if (isset($config[$key])) {
                $container->$method((bool) $config[$key]);
            }
        }

        foreach (self::$arraySetters as $key => $method) {
            if (isset($config[$key]) && is_array($config[$key])) {
                foreach ($config[$key] as $name => $value) {
                    $container->$method($name, $value);
                }
            }
        }

        if (isset($config[Container::CONFIG_ENTRIES]) && is_array($config[Container::CONFIG_ENTRIES])) {
            self::createEntries($container, $config[Container::CONFIG_ENTRIES]);
        }

        return $container;
    }

    /**
     * Create entries.
     *
     * @param \Phower\Container\Container $container
     * @param array $entries
     * @throws Exception\InvalidArgumentException
     */
    protected static function createEntries(Container $container, array $entries)
    {
        $defaultShared = $container->isSharedByDefault();

        foreach ($entries as $entry) {
            foreach (self::$requiredEntryKeys as $key) {
                if (!isset($entry[$key])) {
                    $message = sprintf('Missing required entry key "%s" in "%s".', $key, __METHOD__);
                    throw new Exception\InvalidArgumentException($message);
                }
            }

            if (!in_array($entry[ContainerInterface::CONFIG_ENTRY_TYPE], self::$entryTypes)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Value "%s" for "%s" is not valid; it must be one of "%s" in "%s".',
                    $entry[ContainerInterface::CONFIG_ENTRY_TYPE],
                    $key,
                    implode(', ', self::$types),
                    __METHOD__
                ));
            }

            $shared = isset($entry[ContainerInterface::CONFIG_ENTRY_SHARED]) ? (bool) $entry[ContainerInterface::CONFIG_ENTRY_SHARED] : $defaultShared;

            $container->add($entry[ContainerInterface::CONFIG_ENTRY_NAME], $entry[ContainerInterface::CONFIG_ENTRY_VALUE], $entry[ContainerInterface::CONFIG_ENTRY_TYPE], $shared);
        }
    }
}
