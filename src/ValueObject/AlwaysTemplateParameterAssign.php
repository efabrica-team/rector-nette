<?php

declare(strict_types=1);

namespace Rector\Nette\ValueObject;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;

final class AlwaysTemplateParameterAssign
{
    public function __construct(
        private Assign $assign,
        private string $parameterName,
        private Expr $assignedExpr
    ) {
    }

    public function getAssign(): Assign
    {
        return $this->assign;
    }

    public function getParameterName(): string
    {
        return $this->parameterName;
    }

    public function getAssignedExpr(): Expr
    {
        return $this->assignedExpr;
    }
}
