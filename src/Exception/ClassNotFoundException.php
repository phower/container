<?php

/**
 * Phower Container
 *
 * @version 0.0.0
 * @link https://github.com/phower/container Public Git repository
 * @copyright (c) 2015-2016, Pedro Ferreira <https://phower.com>
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Phower\Container\Exception;

/**
 * Class not found exception
 *
 * @author Pedro Ferreira <pedro@phower.com>
 */
class ClassNotFoundException extends InvalidArgumentException
{

    public function __construct($class)
    {
        $type = is_object($class) ? get_class($class) : gettype($class);
        $message = sprintf('Class "%s" not found.', $type);
        parent::__construct($message);
    }
}
