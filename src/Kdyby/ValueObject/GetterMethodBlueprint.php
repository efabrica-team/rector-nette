<?php

declare(strict_types=1);

namespace Rector\Nette\Kdyby\ValueObject;

use PhpParser\Node;

final class GetterMethodBlueprint
{
    public function __construct(
        private string $methodName,
        private ?\PhpParser\Node $returnTypeNode,
        private string $variableName
    ) {
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getReturnTypeNode(): ?Node
    {
        return $this->returnTypeNode;
    }

    public function getVariableName(): string
    {
        return $this->variableName;
    }
}
