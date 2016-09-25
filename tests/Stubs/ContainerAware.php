<?php

namespace PhowerTest\Container\Stubs;

class ContainerAware implements \Phower\Container\ContainerAwareInterface
{

    use \Phower\Container\ContainerAwareTrait;

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
