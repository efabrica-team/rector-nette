<?php

namespace RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Fixture;

use RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source\ISomeInterfaceToInject;

class RespectInjectPresenter
{
    /**
     * @var \Nette\DI\Container
     */
    private $context;

    /**
     * @inject
     * @var \RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source\SomeTypeToInject
     */
    public $otherInjectedService;

    public function run()
    {
        $someTypeToInject = $this->context->getByType(ISomeInterfaceToInject::class);
    }
}

?>
-----
<?php

namespace RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Fixture;

use RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source\ISomeInterfaceToInject;

class RespectInjectPresenter
{
    /**
     * @var \Nette\DI\Container
     */
    private $context;

    /**
     * @inject
     * @var \RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source\SomeTypeToInject
     */
    public $otherInjectedService;
    /**
     * @inject
     */
    public \RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source\ISomeInterfaceToInject $someInterfaceToInject;

    public function run()
    {
        $someTypeToInject = $this->someInterfaceToInject;
    }
}

?>
