<?php

namespace Phower\Container\Exception;

class InvalidNameException extends InvalidArgumentException
{

    public function __construct($name)
    {
        $type = is_object($name) ? get_class($name) : gettype($name);
        $message = sprintf('Argument "name" must be a string and cannot be empty; type "%s" was given.', $type);
        parent::__construct($message);
    }

}
