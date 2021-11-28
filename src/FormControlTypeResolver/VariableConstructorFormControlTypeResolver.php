<?php

declare(strict_types=1);

namespace Rector\Nette\FormControlTypeResolver;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\TypeWithClassName;
use Rector\Core\PhpParser\AstResolver;
use Rector\Core\ValueObject\MethodName;
use Rector\Nette\Contract\FormControlTypeResolverInterface;
use Rector\Nette\NodeResolver\MethodNamesByInputNamesResolver;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;
use Symfony\Contracts\Service\Attribute\Required;

final class VariableConstructorFormControlTypeResolver implements FormControlTypeResolverInterface
{
    private MethodNamesByInputNamesResolver $methodNamesByInputNamesResolver;

    public function __construct(
        private NodeTypeResolver $nodeTypeResolver,
        private NodeNameResolver $nodeNameResolver,
        private ReflectionProvider $reflectionProvider,
        private AstResolver $astResolver,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function resolve(Node $node): array
    {
        if (! $node instanceof Variable) {
            return [];
        }

        // handled else-where
        if ($this->nodeNameResolver->isName($node, 'this')) {
            return [];
        }

        $formType = $this->nodeTypeResolver->getType($node);
        if (! $formType instanceof TypeWithClassName) {
            return [];
        }

        $formClassReflection = $this->reflectionProvider->getClass($formType->getClassName());

        if (! $formClassReflection->isSubclassOf('Nette\Application\UI\Form')) {
            return [];
        }

        $classMethod = $this->astResolver->resolveClassMethod($formType->getClassName(), MethodName::CONSTRUCT);
        if ($classMethod === null) {
            return [];
        }

        return $this->methodNamesByInputNamesResolver->resolveExpr($classMethod);
    }

    #[Required]
    public function autowire(MethodNamesByInputNamesResolver $methodNamesByInputNamesResolver): void
    {
        $this->methodNamesByInputNamesResolver = $methodNamesByInputNamesResolver;
    }
}
