<?php

declare(strict_types=1);

namespace Nette\ComponentModel;

if (class_exists('Nette\ComponentModel\Component')) {
    return;
}

class Component extends Container implements \ArrayAccess
{
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {

    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {

    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {

    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {

    }
}
