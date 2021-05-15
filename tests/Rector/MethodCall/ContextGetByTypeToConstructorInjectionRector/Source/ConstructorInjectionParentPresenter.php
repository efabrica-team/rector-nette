<?php

declare(strict_types=1);

namespace Rector\Nette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source;

use Nette\Application\IPresenter;
use Nette\Application\IResponse;
use Nette\Application\Request;

abstract class ConstructorInjectionParentPresenter implements IPresenter
{
    public function __construct(private SomeTypeToInject $someTypeToInject)
    {
    }

    function run(Request $request): IResponse
    {
    }
}
