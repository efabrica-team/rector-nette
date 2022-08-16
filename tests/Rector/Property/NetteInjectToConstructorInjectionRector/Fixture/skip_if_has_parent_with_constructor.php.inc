<?php

namespace RectorNette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Fixture;

use RectorNette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source\ParentWithConstructorDependency;

final class SkipIfHasParentWithConstructorDependency extends ParentWithConstructorDependency
{
    /**
     * @var \RectorNette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source\SomeProduct
     * @inject
     */
    public $someProduct;
}
