<?php

namespace RectorNette\Tests\Rector\ClassMethod\MergeTemplateSetFileToTemplateRenderRector\Fixture;

use Nette\Application\UI\Control;

final class SkipNoSetFile extends Control
{
    public function render()
    {
        $this->template->render();
    }
}
