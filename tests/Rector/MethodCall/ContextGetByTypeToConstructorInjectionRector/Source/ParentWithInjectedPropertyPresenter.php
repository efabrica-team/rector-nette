<?php

declare(strict_types=1);

namespace RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source;

use Nette\Application\IPresenter;
use Nette\Application\IResponse;
use Nette\Application\Request;

class ParentWithInjectedPropertyPresenter implements IPresenter
{
    /**
     * @inject
     */
    public \RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source\SomeTypeToInject $someTypeToInject;

    function run(Request $request): IResponse
    {
    }
}
