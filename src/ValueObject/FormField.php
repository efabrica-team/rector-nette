<?php

declare(strict_types=1);

namespace RectorNette\ValueObject;

final class FormField
{
    public function __construct(
        private string $name,
        private string $type,
        private bool $isRequired,
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
