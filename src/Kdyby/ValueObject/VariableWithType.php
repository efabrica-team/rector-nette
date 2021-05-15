<?php

declare(strict_types=1);

namespace Rector\Nette\Kdyby\ValueObject;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\UnionType;
use PHPStan\Type\Type;

final class VariableWithType
{
    /**
     * @param Identifier|Name|NullableType|UnionType|null $phpParserTypeNode
     */
    public function __construct(
        private string $name,
        private Type $type,
        private $phpParserTypeNode
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    /**
     * @return Identifier|Name|NullableType|UnionType|null
     */
    public function getPhpParserTypeNode(): ?Node
    {
        return $this->phpParserTypeNode;
    }
}
