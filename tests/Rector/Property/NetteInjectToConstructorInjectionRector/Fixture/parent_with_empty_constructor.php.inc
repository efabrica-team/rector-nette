<?php

namespace RectorNette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Fixture;

use RectorNette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source\ParentWithEmptyConstructor;

final class SkipIfHasParentWithConstructorDependency extends ParentWithEmptyConstructor
{
    /**
     * @var \RectorNette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source\SomeProduct
     * @inject
     */
    public $someProduct;
}

?>
-----
<?php

namespace RectorNette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Fixture;

use RectorNette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source\ParentWithEmptyConstructor;

final class SkipIfHasParentWithConstructorDependency extends ParentWithEmptyConstructor
{
    public function __construct(private \RectorNette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source\SomeProduct $someProduct)
    {
        parent::__construct();
    }
}

?>
