<?php

declare(strict_types=1);

namespace Rector\Nette\FormControlTypeResolver;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\TypeWithClassName;
use Rector\Core\ValueObject\MethodName;
use Rector\Nette\Contract\FormControlTypeResolverInterface;
use Rector\Nette\NodeResolver\MethodNamesByInputNamesResolver;
use Rector\NodeCollector\NodeCollector\NodeRepository;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;

final class VariableConstructorFormControlTypeResolver implements FormControlTypeResolverInterface
{
    private MethodNamesByInputNamesResolver $methodNamesByInputNamesResolver;

    public function __construct(
        private NodeTypeResolver $nodeTypeResolver,
        private NodeNameResolver $nodeNameResolver,
        private NodeRepository $nodeRepository,
        private ReflectionProvider $reflectionProvider
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

        $formType = $this->nodeTypeResolver->getStaticType($node);
        if (! $formType instanceof TypeWithClassName) {
            return [];
        }

        $formClassReflection = $this->reflectionProvider->getClass($formType->getClassName());

        if (! $formClassReflection->isSubclassOf('Nette\Application\UI\Form')) {
            return [];
        }

        $constructorClassMethod = $this->nodeRepository->findClassMethod(
            $formType->getClassName(),
            MethodName::CONSTRUCT
        );
        if (! $constructorClassMethod instanceof ClassMethod) {
            return [];
        }

        return $this->methodNamesByInputNamesResolver->resolveExpr($constructorClassMethod);
    }

    /**
     * @required
     */
    public function autowireVariableConstructorFormControlTypeResolver(
        MethodNamesByInputNamesResolver $methodNamesByInputNamesResolver
    ): void {
        $this->methodNamesByInputNamesResolver = $methodNamesByInputNamesResolver;
    }
}
