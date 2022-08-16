<?php

namespace RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Fixture;

use RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source\ConstructorInjectionParentPresenter;
use RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source\ISomeInterfaceToInject;

final class RespectInjectParentConstructPreferLocalConstructorPresenter extends ConstructorInjectionParentPresenter
{
    /**
     * @var \Nette\DI\Container
     */
    private $context;

    public function __construct()
    {
        $some = 100;
    }

    public function go()
    {
        $someTypeToInject = $this->context->getByType(ISomeInterfaceToInject::class);
    }
}

?>
-----
<?php

namespace RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Fixture;

use RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source\ConstructorInjectionParentPresenter;
use RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source\ISomeInterfaceToInject;

final class RespectInjectParentConstructPreferLocalConstructorPresenter extends ConstructorInjectionParentPresenter
{
    /**
     * @var \Nette\DI\Container
     */
    private $context;

    public function __construct(\RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source\SomeTypeToInject $someTypeToInject, private \RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source\ISomeInterfaceToInject $someInterfaceToInject)
    {
        $some = 100;
        parent::__construct($someTypeToInject);
    }

    public function go()
    {
        $someTypeToInject = $this->someInterfaceToInject;
    }
}

?>
