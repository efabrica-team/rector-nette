<?php

declare(strict_types=1);

namespace Rector\Nette\Kdyby\ValueObject;

use PhpParser\Node;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\UnionType;
use PHPStan\Type\Type;

final class VariableWithType
{
    /**
     * @param ComplexType|Identifier|Name|NullableType|UnionType|null $phpParserTypeNode
     */
    public function __construct(
        private readonly string $name,
        private readonly Type $type,
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
     * @return ComplexType|Identifier|Name|NullableType|UnionType|null
     */
    public function getPhpParserTypeNode(): ?Node
    {
        return $this->phpParserTypeNode;
    }
}
