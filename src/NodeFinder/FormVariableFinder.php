<?php

declare(strict_types=1);

namespace Rector\Nette\NodeFinder;

use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Type\ObjectType;
use Rector\NodeTypeResolver\NodeTypeResolver;

/**
 * @see \Rector\Nette\Tests\NodeFinder\FormFinder\FormFinderTest
 */
final class FormVariableFinder
{
    public function __construct(
        private NodeTypeResolver $nodeTypeResolver
    ) {
    }

    public function find(Class_ $class): ?Variable
    {
        foreach ($class->getMethods() as $method) {
            $stmts = $method->stmts ?: [];
            foreach ($stmts as $stmt) {
                if (! $stmt instanceof Expression) {
                    continue;
                }

                if (! $stmt->expr instanceof Assign) {
                    continue;
                }

                $var = $stmt->expr->var;
                $expr = $stmt->expr->expr;

                if (! $var instanceof Variable) {
                    continue;
                }

                if (! $this->nodeTypeResolver->isObjectType($expr, new ObjectType('Nette\Forms\Form'))) {
                    continue;
                }

                return $var;
            }
        }
        return null;
    }
}
