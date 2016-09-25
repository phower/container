<?php

namespace Phower\Container\Exception;

class NotAllowedException extends RuntimeException
{

    public function __construct()
    {
        $message = 'Container does not allow override existing names.';
        parent::__construct($message);
    }
}
