<?php

namespace RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Fixture;

use RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source\ParentWithSomeTypeToInjectPropertyPresenter;
use RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source\SomeTypeToInject;

class ReuseParentPropertyWithSameType extends ParentWithSomeTypeToInjectPropertyPresenter
{
    /**
     * @var \Nette\DI\Container
     */
    private $context;

    public function go()
    {
        $someTypeToInject = $this->context->getByType(SomeTypeToInject::class);
    }
}

?>
-----
<?php

namespace RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Fixture;

use RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source\ParentWithSomeTypeToInjectPropertyPresenter;
use RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source\SomeTypeToInject;

class ReuseParentPropertyWithSameType extends ParentWithSomeTypeToInjectPropertyPresenter
{
    /**
     * @var \Nette\DI\Container
     */
    private $context;

    public function go()
    {
        $someTypeToInject = $this->someTypeToInject;
    }
}

?>
