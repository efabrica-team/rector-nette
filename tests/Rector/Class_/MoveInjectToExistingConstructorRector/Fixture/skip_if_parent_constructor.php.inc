<?php

namespace RectorNette\Tests\Rector\Class_\MoveInjectToExistingConstructorRector\Fixture;

use RectorNette\Tests\Rector\Class_\MoveInjectToExistingConstructorRector\Source\ClassWithParentConstructor;

final class SkipIfParentConstructor extends ClassWithParentConstructor
{
    /**
     * @var SomeDependency
     * @inject
     */
    public $someDependency;
}
