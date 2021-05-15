<?php

declare(strict_types=1);

namespace Rector\Nette\ValueObject;

use PhpParser\Node\Expr;

final class ContentExprAndNeedleExpr
{
    public function __construct(
        private Expr $contentExpr,
        private Expr $needleExpr
    ) {
    }

    public function getContentExpr(): Expr
    {
        return $this->contentExpr;
    }

    public function getNeedleExpr(): Expr
    {
        return $this->needleExpr;
    }
}
