<?php

declare(strict_types=1);

namespace RectorNette\NodeAnalyzer;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Core\PhpParser\Node\BetterNodeFinder;
use Rector\NodeNameResolver\NodeNameResolver;

final class RenderMethodAnalyzer
{
    public function __construct(
        private NodeNameResolver $nodeNameResolver,
        private BetterNodeFinder $betterNodeFinder
    ) {
    }

    /**
     * @return MethodCall[]
     */
    public function machRenderMethodCalls(ClassMethod $classMethod): array
    {
        /** @var MethodCall[] $methodsCalls */
        $methodsCalls = $this->betterNodeFinder->findInstanceOf((array) $classMethod->stmts, MethodCall::class);

        $renderMethodCalls = [];
        foreach ($methodsCalls as $methodCall) {
            if ($this->nodeNameResolver->isName($methodCall->name, 'render')) {
                $renderMethodCalls[] = $methodCall;
            }
        }

        return $renderMethodCalls;
    }
}
