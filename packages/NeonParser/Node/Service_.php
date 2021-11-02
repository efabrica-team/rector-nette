<?php

declare(strict_types=1);

namespace Rector\Nette\NeonParser\Node;

use Nette\Neon\Node;
use Nette\Neon\Node\LiteralNode;
use Rector\Nette\NeonParser\Exception\NotImplementedYetException;
use Rector\Nette\NeonParser\Node\Service_\SetupMethodCall;

/**
 * Metanode for easier subscribing
 */
final class Service_ extends AbstractVirtualNode
{
    /**
     * @param SetupMethodCall[] $setupMethodCalls
     */
    public function __construct(
        private string $className,
        private LiteralNode|null $classLiteralNode,
        private LiteralNode|null $factoryLiteralNode,
        private array $setupMethodCalls
    ) {
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getServiceType(): string
    {
        if ($this->classLiteralNode) {
            return $this->classLiteralNode->toString();
        }

        if ($this->factoryLiteralNode) {
            return $this->factoryLiteralNode->toString();
        }

        throw new NotImplementedYetException();
    }

    /**
     * @return SetupMethodCall[]
     */
    public function getSetupMethodCalls(): array
    {
        return $this->setupMethodCalls;
    }

    /**
     * @return Node[]
     */
    public function getSubNodes(): array
    {
        $subNodes = [];
        if ($this->classLiteralNode instanceof LiteralNode) {
            $subNodes[] = $this->classLiteralNode;
        }

        if ($this->factoryLiteralNode instanceof LiteralNode) {
            $subNodes[] = $this->factoryLiteralNode;
        }

        return array_merge($subNodes, $this->setupMethodCalls);
    }
}
