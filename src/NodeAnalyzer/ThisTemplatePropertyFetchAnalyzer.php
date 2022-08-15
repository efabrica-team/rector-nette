<?php

declare(strict_types=1);

namespace RectorNette\NodeAnalyzer;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use Rector\NodeNameResolver\NodeNameResolver;

final class ThisTemplatePropertyFetchAnalyzer
{
    public function __construct(
        private NodeNameResolver $nodeNameResolver
    ) {
    }

    public function resolveTemplateParameterNameFromAssign(Assign $assign): ?string
    {
        if (! $assign->var instanceof PropertyFetch) {
            return null;
        }

        $propertyFetch = $assign->var;
        if (! $this->isTemplatePropertyFetch($propertyFetch->var)) {
            return null;
        }

        return $this->nodeNameResolver->getName($propertyFetch);
    }

    /**
     * Looks for: $this->template
     *
     * $template
     */
    private function isTemplatePropertyFetch(Expr $expr): bool
    {
        if (! $expr instanceof PropertyFetch) {
            return false;
        }

        if (! $expr->var instanceof Variable) {
            return false;
        }

        if (! $this->nodeNameResolver->isName($expr->var, 'this')) {
            return false;
        }

        return $this->nodeNameResolver->isName($expr->name, 'template');
    }
}
