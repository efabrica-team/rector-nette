<?php

declare(strict_types=1);

namespace Rector\Nette\Contract\Rector;

use Nette\Neon\Node;
use Rector\Core\Contract\Rector\RectorInterface;

interface NeonRectorInterface extends RectorInterface
{
    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string;

    public function enterNode(Node $node): Node|null;
}
