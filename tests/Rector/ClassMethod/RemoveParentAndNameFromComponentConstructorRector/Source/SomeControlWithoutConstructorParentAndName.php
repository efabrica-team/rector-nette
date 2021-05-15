<?php
declare(strict_types=1);

namespace Rector\Nette\Tests\Rector\ClassMethod\RemoveParentAndNameFromComponentConstructorRector\Source;

use Nette\Application\UI\Control;

final class SomeControlWithoutConstructorParentAndName extends Control
{
    public function __construct(private $key, private $value)
    {
    }
}
