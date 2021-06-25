<?php

declare(strict_types=1);

namespace Rector\Nette\FormControlTypeResolver;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Type\TypeWithClassName;
use Rector\Nette\Contract\FormControlTypeResolverInterface;
use Rector\Nette\Naming\NetteControlNaming;
use Rector\Nette\NodeAnalyzer\ControlDimFetchAnalyzer;
use Rector\NodeCollector\NodeCollector\NodeRepository;
use Rector\NodeTypeResolver\NodeTypeResolver;
use Rector\TypeDeclaration\TypeInferer\ReturnTypeInferer;

final class ArrayDimFetchControlTypeResolver implements FormControlTypeResolverInterface
{
    public function __construct(
        private ControlDimFetchAnalyzer $controlDimFetchAnalyzer,
        private NetteControlNaming $netteControlNaming,
        private NodeTypeResolver $nodeTypeResolver,
        private ReturnTypeInferer $returnTypeInferer,
        private NodeRepository $nodeRepository
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function resolve(Node $node): array
    {
        if (! $node instanceof ArrayDimFetch) {
            return [];
        }

        $controlShortName = $this->controlDimFetchAnalyzer->matchName($node);
        if ($controlShortName === null) {
            return [];
        }

        $createComponentClassMethod = $this->matchCreateComponentClassMethod($node, $controlShortName);
        if (! $createComponentClassMethod instanceof ClassMethod) {
            return [];
        }

        $createComponentClassMethodReturnType = $this->returnTypeInferer->inferFunctionLike(
            $createComponentClassMethod
        );

        if (! $createComponentClassMethodReturnType instanceof TypeWithClassName) {
            return [];
        }

        return [
            $controlShortName => $createComponentClassMethodReturnType->getClassName(),
        ];
    }

    private function matchCreateComponentClassMethod(
        ArrayDimFetch $arrayDimFetch,
        string $controlShortName
    ): ?ClassMethod {
        $callerType = $this->nodeTypeResolver->getStaticType($arrayDimFetch->var);
        if (! $callerType instanceof TypeWithClassName) {
            return null;
        }

        $createComponentClassMethodName = $this->netteControlNaming->createCreateComponentClassMethodName(
            $controlShortName
        );

        $class = $this->nodeRepository->findClassLike($callerType->getClassName());
        if (! $class instanceof ClassLike) {
            return null;
        }

        return $class->getMethod($createComponentClassMethodName);
    }
}
