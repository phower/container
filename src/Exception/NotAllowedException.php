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
 * Not allowed exception
 *
 * @author Pedro Ferreira <pedro@phower.com>
 */
class NotAllowedException extends RuntimeException
{

    public function __construct()
    {
        $message = 'Container does not allow override existing names.';
        parent::__construct($message);
    }
}
