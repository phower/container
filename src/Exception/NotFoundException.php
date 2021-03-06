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

use Psr\Container\NotFoundExceptionInterface as PsrNotFoundExceptionInterface;

/**
 * Not found exception
 *
 * @author Pedro Ferreira <pedro@phower.com>
 */
class NotFoundException extends RuntimeException implements PsrNotFoundExceptionInterface
{

    public function __construct($name)
    {
        $message = sprintf('Container is unable to find an entry with name "%s".', $name);
        parent::__construct($message);
    }
}
