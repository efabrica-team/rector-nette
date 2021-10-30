<?php

declare(strict_types=1);

namespace Rector\Nette\NeonParser\NeonNodeVisitor;

use Nette\Neon\Node;
use Nette\Neon\Node\LiteralNode;
use Rector\Nette\NeonParser\Contract\NeonNodeVisitorInterface;
use Rector\Nette\NeonParser\Node\Service_;
use Rector\Nette\NeonParser\Node\Service_\SetupMethodCall;
use Rector\Renaming\Collector\MethodCallRenameCollector;
use Rector\Renaming\Contract\MethodCallRenameInterface;

final class RenameMethodCallNeonNodeVisitor implements NeonNodeVisitorInterface
{
    /**
     * @var string
     */
    private const SETUP_KEYWORD = 'setup';

    public function __construct(
        //private ServiceTypeResolver $serviceTypeResolver,
        private MethodCallRenameCollector $methodCallRenameCollector,
    ) {
    }

    /**
     * @return class-string<\PhpParser\Node>
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
        dump($node);
        die;

        $serviceType = $node->getServiceType();

        $methodCallRenames = $this->findMethodCallRenamesByClass($serviceType);
        if ($methodCallRenames === []) {
            return $node;
        }

        // rename method calls
        // $this->renameMethodCalls($methodCallRenames, $serviceNode);
        foreach ($methodCallRenames as $methodCallRename) {
            foreach ($node->getSetupMethodCalls() as $setupMethodCall) {
                if ($setupMethodCall->methodNameLiteralNode->toString() !== $methodCallRename->getOldMethod()) {
                    continue;
                }

                $setupMethodCall->methodNameLiteralNode->value = $methodCallRename->getNewMethod();
            }
        }

        return $node;
    }

    /**
     * @return MethodCallRenameInterface[]
     */
    private function findMethodCallRenamesByClass(string $class): array
    {
        $methodCallRenames = [];

        foreach ($this->methodCallRenameCollector->getMethodCallRenames() as $methodCallRename) {
            $objectType = $methodCallRename->getOldObjectType();
            if ($objectType->getClassName() === $class) {
                $methodCallRenames[] = $methodCallRename;
            }
        }

        return $methodCallRenames;
    }
}
