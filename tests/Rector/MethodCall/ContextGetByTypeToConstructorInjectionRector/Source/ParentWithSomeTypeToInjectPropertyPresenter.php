<?php

declare(strict_types=1);

namespace RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source;

use Nette\Application\IPresenter;
use Nette\Application\IResponse;
use Nette\Application\Request;

class ParentWithSomeTypeToInjectPropertyPresenter implements IPresenter
{
    public function __construct(protected SomeTypeToInject $someTypeToInject)
    {
    }

    public function run(Request $request): IResponse
    {
    }
}
