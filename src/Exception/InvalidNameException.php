<?php

/**
 * Phower Container
 *
 * @version 1.0.0
 * @link https://github.com/phower/container Public Git repository
 * @copyright (c) 2015-2016, Pedro Ferreira <https://phower.com>
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Phower\Container\Exception;

/**
 * Invalid name exception
 *
 * @author Pedro Ferreira <pedro@phower.com>
 */
class InvalidNameException extends InvalidArgumentException
{

    public function __construct($name)
    {
        $type = is_object($name) ? get_class($name) : gettype($name);
        $message = sprintf('Argument "name" must be a string and cannot be empty; type "%s" was given.', $type);
        parent::__construct($message);
    }
}
