<?php

declare(strict_types=1);

namespace Rector\Tests\NetteCodeQuality\Rector\ArrayDimFetch\ChangeFormArrayAccessToAnnotatedControlVariableRector\Source;

use Nette\Application\UI\Form;

final class FormWithTitleLocal extends Form
{
    public function __construct()
    {
        $this->addText('title');
    }
}
