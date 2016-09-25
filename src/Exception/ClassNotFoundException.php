<?php

namespace Phower\Container\Exception;

class ClassNotFoundException extends InvalidArgumentException
{

    public function __construct($class)
    {
        $type = is_object($class) ? get_class($class) : gettype($class);
        $message = sprintf('Class "%s" not found.', $type);
        parent::__construct($message);
    }
}
