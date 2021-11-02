<?php

declare(strict_types=1);

namespace Rector\Nette\FormControlTypeResolver;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Core\PhpParser\Node\BetterNodeFinder;
use Rector\Core\ValueObject\MethodName;
use Rector\Nette\Contract\FormControlTypeResolverInterface;
use Rector\Nette\NodeResolver\MethodNamesByInputNamesResolver;
use Rector\NodeNameResolver\NodeNameResolver;
use Symfony\Contracts\Service\Attribute\Required;

final class ConstructorFormControlTypeResolver implements FormControlTypeResolverInterface
{
    private MethodNamesByInputNamesResolver $methodNamesByInputNamesResolver;

    public function __construct(
        private BetterNodeFinder $betterNodeFinder,
        private NodeNameResolver $nodeNameResolver
    ) {
    }

    #[Required]
    public function autowireConstructorFormControlTypeResolver(
        MethodNamesByInputNamesResolver $methodNamesByInputNamesResolver
    ): void {
        $this->methodNamesByInputNamesResolver = $methodNamesByInputNamesResolver;
    }

    /**
     * @return array<string, string>
     */
    public function resolve(Node $node): array
    {
        if (! $node instanceof ClassMethod) {
            return [];
        }

        if (! $this->nodeNameResolver->isName($node, MethodName::CONSTRUCT)) {
            return [];
        }

        $thisVariable = $this->betterNodeFinder->findVariableOfName($node, 'this');
        if (! $thisVariable instanceof Variable) {
            return [];
        }

        return $this->methodNamesByInputNamesResolver->resolveExpr($thisVariable);
    }
}
