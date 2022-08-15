<?php
declare(strict_types=1);

namespace RectorNette\Tests\Rector\ClassMethod\RemoveParentAndNameFromComponentConstructorRector\Source;

use Nette\Application\UI\Control;

final class SomeControlWithoutConstructorParentAndName extends Control
{
    public function __construct(private $key, private $value)
    {
    }
}
