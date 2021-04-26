<?php

namespace Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\FixturePhp74;

use stdClass;

class TypedProperty
{
    /**
     * @inject
     */
    public stdClass $property;
}

?>
-----
<?php

namespace Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\FixturePhp74;

use stdClass;

class TypedProperty
{
    public function __construct(private \stdClass $property)
    {
    }
}

?>
