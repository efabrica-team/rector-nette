<?php

declare(strict_types=1);

namespace Rector\Nette\ValueObject;

final class LatteVariableType
{
    public function __construct(
        private string $name,
        private string $type
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
