<?php

declare(strict_types=1);

namespace Rector\Nette\NeonParser\Node;

use Nette\Neon\Node;
use Rector\Nette\NeonParser\Exception\UnusedVirtualMethodException;

abstract class AbstractVirtualNode extends Node
{
    /**
     * @param  callable(self): mixed|null  $evaluator
     */
    public function toValue(callable $evaluator = null): mixed
    {
        // never used, just to make parent contract happy
        throw new UnusedVirtualMethodException();
    }

    /**
     * @param  callable(self): string|null  $serializer
     */
    public function toString(callable $serializer = null): string
    {
        // never used, just to make parent contract happy
        throw new UnusedVirtualMethodException();
    }
}
