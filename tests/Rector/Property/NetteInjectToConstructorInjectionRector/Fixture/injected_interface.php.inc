<?php

namespace RectorNette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Fixture;

use RectorNette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source\SomeProductWithInterface;

class ClassWithInjects4
{
    /**
     * @var SomeProductWithInterface
     * @inject
     */
    protected $property;
}

?>
-----
<?php

namespace RectorNette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Fixture;

use RectorNette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source\SomeProductWithInterface;

class ClassWithInjects4
{
    public function __construct(private \RectorNette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source\SomeProductWithInterface $property)
    {
    }
}

?>
