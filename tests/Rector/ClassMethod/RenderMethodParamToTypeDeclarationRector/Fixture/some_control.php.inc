<?php

namespace Rector\Nette\Tests\Rector\ClassMethod\RenderMethodParamToTypeDeclarationRector\Fixture;

use Nette\Application\UI\Control;

final class SomeControl extends Control
{
    /**
     * @param string $name
     */
    public function render($name)
    {
    }
}

?>
-----
<?php

namespace Rector\Nette\Tests\Rector\ClassMethod\RenderMethodParamToTypeDeclarationRector\Fixture;

use Nette\Application\UI\Control;

final class SomeControl extends Control
{
    public function render(string $name)
    {
    }
}

?>
