<?php

declare(strict_types=1);

namespace Rector\Nette\Kdyby\ValueObject;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Property;

final class EventAndListenerTree
{
    /**
     * @param array<class-string, ClassMethod[]> $listenerMethodsByEventSubscriberClass
     * @param GetterMethodBlueprint[] $getterMethodBlueprints
     */
    public function __construct(
        private MethodCall $magicDispatchMethodCall,
        private ?\PhpParser\Node\Stmt\Property $onMagicProperty,
        private string $eventClassName,
        private string $eventFileLocation,
        private Namespace_ $eventClassInNamespace,
        private MethodCall $eventDispatcherDispatchMethodCall,
        private array $listenerMethodsByEventSubscriberClass,
        private array $getterMethodBlueprints
    ) {
    }

    public function getEventClassName(): string
    {
        return $this->eventClassName;
    }

    /**
     * @return ClassMethod[]
     */
    public function getListenerClassMethodsByClass(string $className): array
    {
        return $this->listenerMethodsByEventSubscriberClass[$className] ?? [];
    }

    public function getOnMagicProperty(): ?Property
    {
        return $this->onMagicProperty;
    }

    public function getEventFileLocation(): string
    {
        return $this->eventFileLocation;
    }

    public function getMagicDispatchMethodCall(): MethodCall
    {
        return $this->magicDispatchMethodCall;
    }

    public function getEventClassInNamespace(): Namespace_
    {
        return $this->eventClassInNamespace;
    }

    public function getEventDispatcherDispatchMethodCall(): MethodCall
    {
        return $this->eventDispatcherDispatchMethodCall;
    }

    /**
     * @return GetterMethodBlueprint[]
     */
    public function getGetterMethodBlueprints(): array
    {
        return $this->getterMethodBlueprints;
    }
}
