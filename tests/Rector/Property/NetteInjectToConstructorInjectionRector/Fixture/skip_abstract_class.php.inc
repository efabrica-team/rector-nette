<?php

namespace RectorNette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Fixture;

use RectorNette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source\SomeProductWithParent;

abstract class SkipAbstractClass
{
    /**
     * @var SomeProductWithParent
     * @inject
     */
    protected $property;
}
