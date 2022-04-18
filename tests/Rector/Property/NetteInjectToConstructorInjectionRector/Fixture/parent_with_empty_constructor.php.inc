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
    /**
     * @var \Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source\SomeProduct
     */
    private $someProduct;

    public function __construct(\Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source\SomeProduct $someProduct)
    {
        $this->someProduct = $someProduct;
    }
}

?>
