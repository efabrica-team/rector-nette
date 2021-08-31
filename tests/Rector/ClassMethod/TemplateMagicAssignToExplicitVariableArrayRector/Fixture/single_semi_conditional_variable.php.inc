<?php

namespace Rector\Nette\Tests\Rector\ClassMethod\TemplateMagicAssignToExplicitVariableArrayRector\Fixture;

use Nette\Application\UI\Control;

final class SingleSemiConditionalVariable extends Control
{
    public function render()
    {
        $this->template->someKey = 'old';
        if (mt_rand(0, 1000)) {
            $this->template->someKey = 'maybe';
        }

        $this->template->render(__DIR__ . '/someFile.latte');
    }
}

?>
-----
<?php

namespace Rector\Nette\Tests\Rector\ClassMethod\TemplateMagicAssignToExplicitVariableArrayRector\Fixture;

use Nette\Application\UI\Control;

final class SingleSemiConditionalVariable extends Control
{
    public function render()
    {
        $someKey = 'old';
        if (mt_rand(0, 1000)) {
            $someKey = 'maybe';
        }

        $this->template->render(__DIR__ . '/someFile.latte', ['someKey' => $someKey]);
    }
}

?>
