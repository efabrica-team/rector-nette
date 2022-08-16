<?php

namespace RectorNette\Tests\Rector\ClassMethod\TemplateMagicAssignToExplicitVariableArrayRector\Fixture;

use Nette\Application\UI\Control;

abstract class SkipAbstractControl extends Control
{
    public function checkRequirements($element): void
    {
    }
}
