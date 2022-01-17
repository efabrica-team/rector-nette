<?php

declare(strict_types=1);

namespace Rector\Nette\NodeFinder;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Param;
use Rector\Core\PhpParser\Comparing\NodeComparator;
use Rector\Core\PhpParser\Node\BetterNodeFinder;
use Rector\NodeTypeResolver\Node\AttributeKey;

final class ParamFinder
{
    public function __construct(
        private readonly BetterNodeFinder $betterNodeFinder,
        private readonly NodeComparator $nodeComparator
    ) {
    }

    /**
     * @param Node|Node[] $nodeHaystack
     */
    public function isInAssign(Node|array $nodeHaystack, Param $param): bool
    {
        $variable = $param->var;

        return (bool) $this->betterNodeFinder->find($nodeHaystack, function (Node $node) use ($variable): bool {
            $parent = $node->getAttribute(AttributeKey::PARENT_NODE);
            if (! $parent instanceof Assign) {
                return false;
            }

            return $this->nodeComparator->areNodesEqual($node, $variable);
        });
    }
}
