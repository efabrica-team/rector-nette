<?php

namespace RectorNette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source;

abstract class ParentWithEmptyConstructor
{
    private int $value;

    public function __construct()
    {
        $this->value = 100;
    }
}
