<?php

namespace RectorNette\Tests\Rector\MethodCall\MagicHtmlCallToAppendAttributeRector\Fixture;

use Nette\Utils\Html;

final class SomeClass
{
    public function run()
    {
        $html = Html::el();
        $html->setClass('first');
    }
}

?>
-----
<?php

namespace RectorNette\Tests\Rector\MethodCall\MagicHtmlCallToAppendAttributeRector\Fixture;

use Nette\Utils\Html;

final class SomeClass
{
    public function run()
    {
        $html = Html::el();
        $html->appendAttribute('class', 'first');
    }
}

?>
