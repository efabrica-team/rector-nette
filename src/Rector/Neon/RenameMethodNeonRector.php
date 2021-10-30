<?php

declare(strict_types=1);

namespace Rector\Nette\Rector\Neon;

use Rector\Nette\Contract\Rector\NeonRectorInterface;
use Rector\Nette\NeonParser\NeonNodeTraverserFactory;
use Rector\Nette\NeonParser\NeonNodeVisitor\RenameMethodCallNeonNodeVisitor;
use Rector\Nette\NeonParser\NeonParser;
use Rector\Nette\NeonParser\Printer\FormatPreservingPrinter;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Rector\Nette\Tests\Rector\Neon\RenameMethodNeonRector\RenameMethodNeonRectorTest
 */
final class RenameMethodNeonRector implements NeonRectorInterface
{
    public function __construct(
        private NeonParser $neonParser,
        private RenameMethodCallNeonNodeVisitor $renameMethodCallNeonNodeVisitor,
        private FormatPreservingPrinter $formatPreservingPrinter,
        private NeonNodeTraverserFactory $neonNodeTraverserFactory
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

    public function changeContent(string $content): string
    {
        $neonNode = $this->neonParser->parseString($content);

        $neonNodeTraverser = $this->neonNodeTraverserFactory->create();
        $neonNodeTraverser->addNeonNodeVisitor($this->renameMethodCallNeonNodeVisitor);
        $neonNode = $neonNodeTraverser->traverse($neonNode);

        return $this->formatPreservingPrinter->printNode($neonNode, $content);
    }
}
