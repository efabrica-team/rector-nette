<?php

declare(strict_types=1);

namespace Rector\Nette\NeonParser\NeonNodeVisitor;

use Nette\Neon\Node;
use Rector\Nette\NeonParser\Contract\NeonNodeVisitorInterface;
use Rector\Nette\NeonParser\Node\Service_\SetupMethodCall;
use Rector\Renaming\Collector\MethodCallRenameCollector;

final class RenameMethodCallNeonNodeVisitor implements NeonNodeVisitorInterface
{
    public function __construct(
        private MethodCallRenameCollector $methodCallRenameCollector,
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return SetupMethodCall::class;
    }

    /**
     * @param SetupMethodCall $node
     */
    public function enterNode(Node $node): Node
    {
        foreach ($this->methodCallRenameCollector->getMethodCallRenames() as $methodCallRename) {
            if (! is_a($node->className, $methodCallRename->getClass(), true)) {
                continue;
            }

            if ($node->getMethodName() !== $methodCallRename->getOldMethod()) {
                continue;
            }

            $node->methodNameLiteralNode->value = $methodCallRename->getNewMethod();
        }

        return $node;
    }
}
