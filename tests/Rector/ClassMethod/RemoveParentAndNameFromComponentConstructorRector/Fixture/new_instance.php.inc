<?php

namespace RectorNette\Tests\Rector\ClassMethod\RemoveParentAndNameFromComponentConstructorRector\Fixture;

use RectorNette\Tests\Rector\ClassMethod\RemoveParentAndNameFromComponentConstructorRector\Source\SomeControlWithConstructorParentAndName;

final class NewInstance
{
    public function run()
    {
        $someControlWithConstructorParentAndName = new SomeControlWithConstructorParentAndName(null, 'hey');
    }
}

?>
-----
<?php

namespace RectorNette\Tests\Rector\ClassMethod\RemoveParentAndNameFromComponentConstructorRector\Fixture;

use RectorNette\Tests\Rector\ClassMethod\RemoveParentAndNameFromComponentConstructorRector\Source\SomeControlWithConstructorParentAndName;

final class NewInstance
{
    public function run()
    {
        $someControlWithConstructorParentAndName = new SomeControlWithConstructorParentAndName();
    }
}

?>
