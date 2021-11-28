<?php

declare(strict_types=1);

namespace Rector\Nette\Contract\Rector;

use Nette\Neon\Node;
use Rector\Core\Contract\Rector\RectorInterface;

/**
 * @template TNode as Node
 */
interface NeonRectorInterface extends RectorInterface
{
    /**
     * @return class-string<TNode>
     */
    public function getNodeType(): string;

    /**
     * @param TNode $node
     */
    public function enterNode(Node $node): Node|null;
}
