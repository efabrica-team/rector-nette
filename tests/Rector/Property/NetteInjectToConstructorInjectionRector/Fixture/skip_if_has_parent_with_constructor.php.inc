<?php

namespace Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Fixture;

use Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source\ParentWithConstructorDependency;

final class SkipIfHasParentWithConstructorDependency extends ParentWithConstructorDependency
{
    /**
     * @var \Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source\SomeProduct
     * @inject
     */
    public $someProduct;
}
