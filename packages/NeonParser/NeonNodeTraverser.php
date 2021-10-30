<?php

declare(strict_types=1);

namespace Rector\Nette\NeonParser;

use Nette\Neon\Node;
use Rector\Nette\NeonParser\Contract\NeonNodeVisitorInterface;

/**
 * @see https://forum.nette.org/en/34804-neon-with-ast-parser-and-format-preserving-printing
 */
final class NeonNodeTraverser
{
    /**
     * @var NeonNodeVisitorInterface[]
     */
    private array $neonNodeVisitors = [];

    public function addNeonNodeVisitor(NeonNodeVisitorInterface $neonNodeVisitor): void
    {
        $this->neonNodeVisitors[] = $neonNodeVisitor;
    }

    public function traverse(Node $node): Node
    {
        foreach ($this->neonNodeVisitors as $neonNodeVisitor) {
            $neonNodeVisitor->enterNode($node);

            // traverse all children
            foreach ($node->getSubNodes() as $subnode) {
                $this->traverse($subnode);
            }
        }

        return $node;
    }
}
