<?php

namespace Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source;

abstract class ParentWithConstructorDependency
{
    public function __construct($value = 100)
    {
    }
}
