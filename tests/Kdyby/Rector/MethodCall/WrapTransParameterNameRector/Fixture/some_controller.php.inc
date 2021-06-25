<?php

namespace Rector\Nette\Tests\Kdyby\Rector\MethodCall\WrapTransParameterNameRector\Fixture;

use Rector\Nette\Tests\Kdyby\Rector\MethodCall\WrapTransParameterNameRector\Source\LocalTranslator;

final class SomeController
{
    public function run()
    {
        $translator = new LocalTranslator('');
        $translated = $translator->trans(
            'Hello %name%',
            ['name' => 'Tom']
        );
    }
}

?>
-----
<?php

namespace Rector\Nette\Tests\Kdyby\Rector\MethodCall\WrapTransParameterNameRector\Fixture;

use Rector\Nette\Tests\Kdyby\Rector\MethodCall\WrapTransParameterNameRector\Source\LocalTranslator;

final class SomeController
{
    public function run()
    {
        $translator = new LocalTranslator('');
        $translated = $translator->trans(
            'Hello %name%',
            ['%name%' => 'Tom']
        );
    }
}

?>
