<?php

namespace Phower\Container\Exception;

class LockedContainerException extends RuntimeException
{

    public function __construct()
    {
        $message = 'Container is locked and can\'t be changed.';
        parent::__construct($message);
    }

}
