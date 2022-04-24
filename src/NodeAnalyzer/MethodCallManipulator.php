<?php

declare(strict_types=1);

namespace Rector\Nette\NodeAnalyzer;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Core\PhpParser\Node\BetterNodeFinder;
use Rector\Defluent\NodeAnalyzer\FluentChainMethodCallNodeAnalyzer;
use Rector\NodeNameResolver\NodeNameResolver;

final class MethodCallManipulator
{
    public function __construct(
        private readonly BetterNodeFinder $betterNodeFinder,
        private readonly NodeNameResolver $nodeNameResolver,
        private readonly FluentChainMethodCallNodeAnalyzer $fluentChainMethodCallNodeAnalyzer
    ) {
    }

    /**
     * @return MethodCall[]
     */
    public function findMethodCallsOnVariable(Variable $variable): array
    {
        // get scope node, e.g. parent function call, method call or anonymous function
        $classMethod = $this->betterNodeFinder->findParentType($variable, ClassMethod::class);
        if (! $classMethod instanceof ClassMethod) {
            return [];
        }

        $variableName = $this->nodeNameResolver->getName($variable);
        if ($variableName === null) {
            return [];
        }

        /** @var MethodCall[] $methodCalls */
        $methodCalls = $this->betterNodeFinder->findInstanceOf($classMethod, MethodCall::class);

        return array_filter($methodCalls, function (MethodCall $methodCall) use ($variableName): bool {
            // cover fluent interfaces too
            $callerNode = $this->fluentChainMethodCallNodeAnalyzer->resolveRootExpr($methodCall);
            if (! $callerNode instanceof Variable) {
                return false;
            }

            return $this->nodeNameResolver->isName($callerNode, $variableName);
        });
    }
}
