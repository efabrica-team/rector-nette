<?php

namespace Rector\Nette\Tests\Rector\ClassMethod\TemplateMagicAssignToExplicitVariableArrayRector\Fixture;

use Nette\Application\UI\Control;

final class SomeControlWithExistingRender extends Control
{
    public function render()
    {
        $this->template->second_param = 'some value';
        $this->template->render(__DIR__ . '/poll.latte', [
            'first_param' => 'another value',
        ]);
    }
}

?>
-----
<?php

namespace Rector\Nette\Tests\Rector\ClassMethod\TemplateMagicAssignToExplicitVariableArrayRector\Fixture;

use Nette\Application\UI\Control;

final class SomeControlWithExistingRender extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/poll.latte', [
            'first_param' => 'another value', 'second_param' => 'some value',
        ]);
    }
}

?>
