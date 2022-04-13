<?php

declare(strict_types=1);

namespace Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source;

abstract class BasePresenterWithConstructor
{
    public function __construct(protected string $baseParameter)
    {
    }
}
