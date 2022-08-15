<?php

namespace RectorNette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source;

abstract class ParentWithConstructorDependency
{
    public function __construct($value = 100)
    {
    }
}
