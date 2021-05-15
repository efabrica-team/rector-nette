<?php

declare(strict_types=1);

namespace Rector\Nette\FormControlTypeResolver;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Type\TypeWithClassName;
use Rector\Core\PhpParser\Node\Value\ValueResolver;
use Rector\Core\ValueObject\MethodName;
use Rector\Nette\Contract\FormControlTypeResolverInterface;
use Rector\Nette\NodeResolver\MethodNamesByInputNamesResolver;
use Rector\NodeCollector\NodeCollector\NodeRepository;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;
use Rector\StaticTypeMapper\ValueObject\Type\FullyQualifiedObjectType;

final class GetComponentMethodCallFormControlTypeResolver implements FormControlTypeResolverInterface
{
    private MethodNamesByInputNamesResolver $methodNamesByInputNamesResolver;

    public function __construct(
        private NodeNameResolver $nodeNameResolver,
        private NodeTypeResolver $nodeTypeResolver,
        private ValueResolver $valueResolver,
        private NodeRepository $nodeRepository
    ) {
    }

    /**
     * @required
     */
    public function autowireGetComponentMethodCallFormControlTypeResolver(
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

        if (! $this->nodeNameResolver->isName($node->name, 'getComponent')) {
            return [];
        }

        $createComponentClassMethodName = $this->createCreateComponentMethodName($node);

        $staticType = $this->nodeTypeResolver->getStaticType($node);

        if (! $staticType instanceof FullyQualifiedObjectType) {
            return [];
        }

        // combine constructor + method body name
        $constructorClassMethodData = [];
        $constructorClassMethod = $this->nodeRepository->findClassMethod(
            $staticType->getClassName(),
            MethodName::CONSTRUCT
        );

        if ($constructorClassMethod !== null) {
            $constructorClassMethodData = $this->methodNamesByInputNamesResolver->resolveExpr($constructorClassMethod);
        }

        $callerType = $this->nodeTypeResolver->getStaticType($node->var);

        $createComponentClassMethodData = [];
        if ($callerType instanceof TypeWithClassName) {
            $createComponentClassMethod = $this->nodeRepository->findClassMethod(
                $callerType->getClassName(),
                $createComponentClassMethodName
            );

            if ($createComponentClassMethod !== null) {
                $createComponentClassMethodData = $this->methodNamesByInputNamesResolver->resolveExpr(
                    $createComponentClassMethod
                );
            }
        }

        return array_merge($constructorClassMethodData, $createComponentClassMethodData);
    }

    private function createCreateComponentMethodName(MethodCall $methodCall): string
    {
        $firstArgumentValue = $methodCall->args[0]->value;

        return 'createComponent' . ucfirst($this->valueResolver->getValue($firstArgumentValue));
    }
}
