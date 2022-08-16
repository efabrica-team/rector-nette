<?php

namespace RectorNette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\FixturePhp74;

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

namespace RectorNette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\FixturePhp74;

use stdClass;

class TypedProperty
{
    public function __construct(private \stdClass $property)
    {
    }
}

?>
