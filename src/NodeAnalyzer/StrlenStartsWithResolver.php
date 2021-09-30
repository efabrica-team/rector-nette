<?php

declare(strict_types=1);

namespace Rector\Nette\NodeAnalyzer;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use Rector\Core\PhpParser\Comparing\NodeComparator;
use Rector\Core\PhpParser\Node\Value\ValueResolver;
use Rector\Nette\ValueObject\ContentExprAndNeedleExpr;
use Rector\NodeNameResolver\NodeNameResolver;

final class StrlenStartsWithResolver
{
    public function __construct(
        private NodeNameResolver $nodeNameResolver,
        private ValueResolver $valueResolver,
        private NodeComparator $nodeComparator
    ) {
    }

    public function resolveBinaryOpForFunction(Identical|NotIdentical $binaryOp, string $functionName): ?ContentExprAndNeedleExpr
    {
        if ($binaryOp->left instanceof Variable) {
            return $this->matchContentExprAndNeedleExpr($binaryOp->right, $binaryOp->left, $functionName);
        }

        if ($binaryOp->right instanceof Variable) {
            return $this->matchContentExprAndNeedleExpr($binaryOp->left, $binaryOp->right, $functionName);
        }

        return null;
    }

    private function matchContentExprAndNeedleExpr(
        Node $node,
        Variable $variable,
        string $functionName
    ): ?ContentExprAndNeedleExpr {
        if (! $node instanceof FuncCall) {
            return null;
        }

        if (! $this->nodeNameResolver->isName($node, $functionName)) {
            return null;
        }

        /** @var FuncCall $node */
        if (! $this->valueResolver->isValue($node->args[1]->value, 0)) {
            return null;
        }

        if (! isset($node->args[2])) {
            return null;
        }

        if (! $node->args[2]->value instanceof FuncCall) {
            return null;
        }

        if (! $this->nodeNameResolver->isName($node->args[2]->value, 'strlen')) {
            return null;
        }

        /** @var FuncCall $strlenFuncCall */
        $strlenFuncCall = $node->args[2]->value;
        if ($this->nodeComparator->areNodesEqual($strlenFuncCall->args[0]->value, $variable)) {
            return new ContentExprAndNeedleExpr($node->args[0]->value, $strlenFuncCall->args[0]->value);
        }

        return null;
    }
}
