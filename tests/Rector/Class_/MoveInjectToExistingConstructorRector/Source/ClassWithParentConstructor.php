<?php

declare(strict_types=1);

namespace RectorNette\Tests\Rector\Class_\MoveInjectToExistingConstructorRector\Source;

abstract class ClassWithParentConstructor
{
    public function __construct()
    {
        $yes = 'no';
    }
}
