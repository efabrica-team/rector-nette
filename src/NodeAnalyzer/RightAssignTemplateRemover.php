<?php

declare(strict_types=1);

namespace Rector\Nette\NodeAnalyzer;

use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Core\PhpParser\Node\BetterNodeFinder;
use Rector\NodeRemoval\NodeRemover;

final class RightAssignTemplateRemover
{
    public function __construct(
        private BetterNodeFinder $betterNodeFinder,
        private ThisTemplatePropertyFetchAnalyzer $thisTemplatePropertyFetchAnalyzer,
        private NodeRemover $nodeRemover
    ) {
    }

    public function removeInClassMethod(ClassMethod $classMethod): void
    {
        /** @var Assign[] $assigns */
        $assigns = $this->betterNodeFinder->findInstanceOf($classMethod, Assign::class);

        foreach ($assigns as $assign) {
            if (! $this->thisTemplatePropertyFetchAnalyzer->isTemplatePropertyFetch($assign->expr)) {
                return;
            }

            $this->nodeRemover->removeNode($assign);
        }
    }
}
