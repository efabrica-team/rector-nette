<?php

namespace Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Fixture;

use Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source\ParentWithEmptyConstructor;

final class SkipIfHasParentWithConstructorDependency extends ParentWithEmptyConstructor
{
    /**
     * @var \Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source\SomeProduct
     * @inject
     */
    public $someProduct;
}

?>
-----
<?php

namespace Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Fixture;

use Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source\ParentWithEmptyConstructor;

final class SkipIfHasParentWithConstructorDependency extends ParentWithEmptyConstructor
{
    public function __construct(private \Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source\SomeProduct $someProduct)
    {
        parent::__construct();
    }
}

?>
