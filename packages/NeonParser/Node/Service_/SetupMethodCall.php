<?php

declare(strict_types=1);

namespace Rector\Nette\NeonParser\Node\Service_;

use Nette\Neon\Node\EntityNode;
use Nette\Neon\Node\LiteralNode;
use Rector\Nette\NeonParser\Node\AbstractVirtualNode;

final class SetupMethodCall extends AbstractVirtualNode
{
    public function __construct(
        public string $className,
        public LiteralNode $methodNameLiteralNode,
        public EntityNode $entityNode
    ) {
    }

    public function getMethodName(): string
    {
        return $this->methodNameLiteralNode->toValue();
    }
}
