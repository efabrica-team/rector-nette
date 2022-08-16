<?php

namespace RectorNette\Tests\Rector\ClassMethod\TemplateMagicAssignToExplicitVariableArrayRector\Fixture;

use Nette\Application\UI\Control;

final class SkipNestedConditional extends Control
{
    public function render()
    {
        if (mt_rand(0, 1000)) {
            if (mt_rand(0, 1000)) {
                $this->template->someKey = 'true';
            } else {
                $this->template->someKey = 'false';
            }
        }

        $this->template->render(__DIR__ . '/someFile.latte');
    }
}
