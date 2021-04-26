<?php

namespace Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Fixture;

use Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source\SomeProductWithParent;

abstract class SkipAbstractClass
{
    /**
     * @var SomeProductWithParent
     * @inject
     */
    protected $property;
}
