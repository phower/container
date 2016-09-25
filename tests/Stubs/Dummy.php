<?php

namespace PhowerTest\Container\Stubs;

class Dummy
{

    private $created;
    private $name;

    public function __construct($name = null)
    {
        $this->created = new \DateTime('now');
        $this->name = $name;
    }

    public static function create()
    {
        return new static();
    }
}
