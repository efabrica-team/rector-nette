<?php

declare(strict_types=1);

namespace Rector\Nette\ValueObject;

use PhpParser\Node\Expr\Assign;

final class ParameterAssign
{
    public function __construct(
        private Assign $assign,
        private string $parameterName
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
}
