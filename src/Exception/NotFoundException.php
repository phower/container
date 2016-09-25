<?php

namespace Phower\Container\Exception;

class NotFoundException extends RuntimeException implements \Interop\Container\Exception\NotFoundException
{

    public function __construct($name)
    {
        $message = sprintf('Container is unable to find an entry with name "%s".', $name);
        parent::__construct($message);
    }
}
