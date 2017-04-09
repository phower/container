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
 * Locked container exception
 *
 * @author Pedro Ferreira <pedro@phower.com>
 */
class LockedContainerException extends RuntimeException
{

    public function __construct()
    {
        $message = 'Container is locked and can\'t be changed.';
        parent::__construct($message);
    }
}
