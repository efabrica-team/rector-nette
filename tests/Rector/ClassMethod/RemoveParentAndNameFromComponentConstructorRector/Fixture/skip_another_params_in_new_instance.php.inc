<?php

declare(strict_types=1);

namespace RectorNette\Tests\Rector\ClassMethod\RemoveParentAndNameFromComponentConstructorRector\Fixture;

use RectorNette\Tests\Rector\ClassMethod\RemoveParentAndNameFromComponentConstructorRector\Source\SomeControlWithoutConstructorParentAndName;

final class SkipAnotherParamInNewInstance
{
    public function run()
    {
        $someControlWithConstructorParentAndName = new SomeControlWithoutConstructorParentAndName('key', 'value');
    }
}
