<?php

declare(strict_types=1);

namespace Rector\Nette\FormControlTypeResolver;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Core\PhpParser\Node\BetterNodeFinder;
use Rector\Core\ValueObject\MethodName;
use Rector\Nette\Contract\FormControlTypeResolverInterface;
use Rector\Nette\NodeResolver\MethodNamesByInputNamesResolver;
use Rector\NodeNameResolver\NodeNameResolver;
use Symfony\Contracts\Service\Attribute\Required;

final class ThisVariableInAnotherMethodFormControlTypeResolver implements FormControlTypeResolverInterface
{
    private MethodNamesByInputNamesResolver $methodNamesByInputNamesResolver;

    public function __construct(
        private NodeNameResolver $nodeNameResolver,
        private BetterNodeFinder $betterNodeFinder,
    ) {
    }

    #[Required]
    public function autowire(MethodNamesByInputNamesResolver $methodNamesByInputNamesResolver): void
    {
        $this->methodNamesByInputNamesResolver = $methodNamesByInputNamesResolver;
    }

    /**
     * @return array<string, string>
     */
    public function resolve(Node $node): array
    {
        if (! $node instanceof Variable) {
            return [];
        }

        $classMethod = $this->betterNodeFinder->findParentType($node, ClassMethod::class);
        if (! $classMethod instanceof ClassMethod) {
            return [];
        }

        // handled elsewhere
        if ($this->nodeNameResolver->isName($classMethod, MethodName::CONSTRUCT)) {
            return [];
        }

        $class = $this->betterNodeFinder->findParentType($node, Class_::class);
        if (! $class instanceof Class_) {
            return [];
        }

        $constructorClassMethod = $class->getMethod(MethodName::CONSTRUCT);
        if (! $constructorClassMethod instanceof ClassMethod) {
            return [];
        }

        return $this->methodNamesByInputNamesResolver->resolveExpr($constructorClassMethod);
    }
}
