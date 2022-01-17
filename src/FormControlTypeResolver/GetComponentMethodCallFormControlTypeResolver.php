<?php

declare(strict_types=1);

namespace Rector\Nette\FormControlTypeResolver;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Type\TypeWithClassName;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\PhpParser\AstResolver;
use Rector\Core\PhpParser\Node\Value\ValueResolver;
use Rector\Core\ValueObject\MethodName;
use Rector\Nette\Contract\FormControlTypeResolverInterface;
use Rector\Nette\NodeResolver\MethodNamesByInputNamesResolver;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;
use Rector\StaticTypeMapper\ValueObject\Type\FullyQualifiedObjectType;
use Symfony\Contracts\Service\Attribute\Required;

final class GetComponentMethodCallFormControlTypeResolver implements FormControlTypeResolverInterface
{
    private MethodNamesByInputNamesResolver $methodNamesByInputNamesResolver;

    public function __construct(
        private readonly NodeNameResolver $nodeNameResolver,
        private readonly NodeTypeResolver $nodeTypeResolver,
        private readonly ValueResolver $valueResolver,
        private readonly AstResolver $astResolver
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
        if (! $node instanceof MethodCall) {
            return [];
        }

        if (! $this->nodeNameResolver->isName($node->name, 'getComponent')) {
            return [];
        }

        $createComponentClassMethodName = $this->createCreateComponentMethodName($node);

        $staticType = $this->nodeTypeResolver->getType($node);

        if (! $staticType instanceof FullyQualifiedObjectType) {
            return [];
        }

        // combine constructor + method body name
        $constructorClassMethodData = [];

        $constructorClassMethod = $this->astResolver->resolveClassMethod(
            $staticType->getClassName(),
            MethodName::CONSTRUCT
        );

        if ($constructorClassMethod !== null) {
            $constructorClassMethodData = $this->methodNamesByInputNamesResolver->resolveExpr($constructorClassMethod);
        }

        $callerType = $this->nodeTypeResolver->getType($node->var);
        if (! $callerType instanceof TypeWithClassName) {
            return $constructorClassMethodData;
        }

        $createComponentClassMethodData = [];

        $createComponentClassMethod = $this->astResolver->resolveClassMethod(
            $callerType->getClassName(),
            $createComponentClassMethodName
        );

        if ($createComponentClassMethod !== null) {
            $createComponentClassMethodData = $this->methodNamesByInputNamesResolver->resolveExpr(
                $createComponentClassMethod
            );
        }

        return [...$constructorClassMethodData, ...$createComponentClassMethodData];
    }

    private function createCreateComponentMethodName(MethodCall $methodCall): string
    {
        $firstArgumentValue = $methodCall->args[0]->value;

        $componentName = $this->valueResolver->getValue($firstArgumentValue);
        if (! is_string($componentName)) {
            throw new ShouldNotHappenException();
        }

        return 'createComponent' . ucfirst($componentName);
    }
}
