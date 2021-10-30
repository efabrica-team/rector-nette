<?php

declare(strict_types=1);

namespace Rector\Nette\NeonParser\Node;

use Nette\Neon\Node;

abstract class AbstractVirtualNode extends Node
{
    // never used, just to make parent contract happy
    public function toValue()
    {
    }

    // never used, just to make parent contract happy
    public function toString(): string
    {
    }
}
