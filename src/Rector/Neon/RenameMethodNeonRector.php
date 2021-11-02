<?php

declare(strict_types=1);

namespace Rector\Nette\Rector\Neon;

use Nette\Neon\Node;
use Rector\Nette\Contract\Rector\NeonRectorInterface;
use Rector\Nette\NeonParser\Node\Service_\SetupMethodCall;
use Rector\Renaming\Collector\MethodCallRenameCollector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Rector\Nette\Tests\Rector\Neon\RenameMethodNeonRector\RenameMethodNeonRectorTest
 */
final class RenameMethodNeonRector implements NeonRectorInterface
{
    public function __construct(
        private MethodCallRenameCollector $methodCallRenameCollector,
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Renames method calls in NEON configs', [
            new CodeSample(
                <<<'CODE_SAMPLE'
services:
    -
        class: SomeClass
        setup:
            - oldCall
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
services:
    -
        class: SomeClass
        setup:
            - newCall
CODE_SAMPLE
            ),
        ]);
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
