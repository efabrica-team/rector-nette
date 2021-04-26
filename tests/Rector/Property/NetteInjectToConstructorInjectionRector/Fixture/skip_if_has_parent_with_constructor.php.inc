<?php

namespace Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Fixture;

final class SkipIfHasParentWithConstructor extends ParentWithConstructor
{
    /**
     * @var \Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source\SomeProduct
     * @inject
     */
    public $someProduct;
}

class ParentWithConstructor
{
    public function __construct($value = 100)
    {
    }
}
