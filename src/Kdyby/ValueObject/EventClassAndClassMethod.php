<?php

declare(strict_types=1);

namespace Rector\Nette\Kdyby\ValueObject;

use PhpParser\Node\Stmt\ClassMethod;

final class EventClassAndClassMethod
{
    public function __construct(
        private readonly string $eventClass,
        private readonly ClassMethod $classMethod
    ) {
    }

    public function getEventClass(): string
    {
        return $this->eventClass;
    }

    public function getClassMethod(): ClassMethod
    {
        return $this->classMethod;
    }
}
