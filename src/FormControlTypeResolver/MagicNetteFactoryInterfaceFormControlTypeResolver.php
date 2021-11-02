<?php

declare(strict_types=1);

namespace Rector\Nette\FormControlTypeResolver;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\TypeWithClassName;
use Rector\Core\PhpParser\AstResolver;
use Rector\Core\ValueObject\MethodName;
use Rector\Nette\Contract\FormControlTypeResolverInterface;
use Rector\Nette\NodeResolver\MethodNamesByInputNamesResolver;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;
use Symfony\Contracts\Service\Attribute\Required;

final class MagicNetteFactoryInterfaceFormControlTypeResolver implements FormControlTypeResolverInterface
{
    private MethodNamesByInputNamesResolver $methodNamesByInputNamesResolver;

    public function __construct(
        private NodeNameResolver $nodeNameResolver,
        private NodeTypeResolver $nodeTypeResolver,
        private ReflectionProvider $reflectionProvider,
        private AstResolver $astResolver,
    ) {
    }

    #[Required]
    public function autowireMagicNetteFactoryInterfaceFormControlTypeResolver(
        MethodNamesByInputNamesResolver $methodNamesByInputNamesResolver
    ): void {
        $this->methodNamesByInputNamesResolver = $methodNamesByInputNamesResolver;
    }

    /**
     * @return array<string, string>
     */
    public function resolve(Node $node): array
    {
        if (! $node instanceof MethodCall) {
            return [];
        }

        // skip constructor, handled elsewhere
        if ($this->nodeNameResolver->isName($node->name, MethodName::CONSTRUCT)) {
            return [];
        }

        $methodName = $this->nodeNameResolver->getName($node->name);
        if ($methodName === null) {
            return [];
        }

        $classReflection = $this->resolveClassReflectionByExpr($node->var);
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        $returnedType = $this->nodeTypeResolver->getType($node);
        if (! $returnedType instanceof TypeWithClassName) {
            return [];
        }

        $classMethod = $this->astResolver->resolveClassMethod($returnedType->getClassName(), MethodName::CONSTRUCT);
        if ($classMethod === null) {
            return [];
        }

        return $this->methodNamesByInputNamesResolver->resolveExpr($classMethod);
    }

    private function resolveClassReflectionByExpr(Expr $expr): ?ClassReflection
    {
        $staticType = $this->nodeTypeResolver->getType($expr);
        if (! $staticType instanceof TypeWithClassName) {
            return null;
        }

        if (! $this->reflectionProvider->hasClass($staticType->getClassName())) {
            return null;
        }

        return $this->reflectionProvider->getClass($staticType->getClassName());
    }
}
