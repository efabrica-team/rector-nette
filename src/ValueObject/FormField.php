<?php

declare(strict_types=1);

namespace Rector\Nette\ValueObject;

final class FormField
{
    public function __construct(
        private readonly string $name,
        private readonly string $type,
        private readonly bool $isRequired,
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

    public function isRequired(): bool
    {
        return $this->isRequired;
    }
}
