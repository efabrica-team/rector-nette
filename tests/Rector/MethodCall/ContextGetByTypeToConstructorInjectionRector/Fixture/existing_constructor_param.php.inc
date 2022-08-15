<?php

namespace RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Fixture;

use RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source\ISomeInterfaceToInject;

class ExistingConstructorParam
{
    /**
     * @var \Nette\DI\Container
     */
    private $context;

    /**
     * @var \RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source\ISomeInterfaceToInject
     */
    private $someInterfaceToInject;

    public function __construct(ISomeInterfaceToInject $someInterfaceToInject)
    {
        $this->someInterfaceToInject = $someInterfaceToInject;
    }

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

class ExistingConstructorParam
{
    /**
     * @var \Nette\DI\Container
     */
    private $context;

    /**
     * @var \RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector\Source\ISomeInterfaceToInject
     */
    private $someInterfaceToInject;

    public function __construct(ISomeInterfaceToInject $someInterfaceToInject)
    {
        $this->someInterfaceToInject = $someInterfaceToInject;
    }

    public function run()
    {
        $someTypeToInject = $this->someInterfaceToInject;
    }
}

?>
