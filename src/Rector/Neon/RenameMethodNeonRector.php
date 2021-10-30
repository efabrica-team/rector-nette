<?php

declare(strict_types=1);

namespace Rector\Nette\Rector\Neon;

use Nette\Neon\Decoder;
use Nette\Neon\Node;
use Nette\Neon\Node\ArrayItemNode;
use Nette\Neon\Node\ArrayNode;
use Nette\Neon\Node\EntityNode;
use Nette\Neon\Node\LiteralNode;
use Nette\Neon\Traverser;
use Nette\Utils\Strings;
use Rector\Nette\Contract\Rector\NeonRectorInterface;
use Rector\Renaming\Collector\MethodCallRenameCollector;
use Rector\Renaming\Contract\MethodCallRenameInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Rector\Nette\Tests\Rector\Neon\RenameMethodNeonRector\RenameMethodNeonRectorTest
 */
final class RenameMethodNeonRector implements NeonRectorInterface
{
    /**
     * @var string
     */
    private const SERVICES_KEYWORD = 'services';

    public function __construct(
        private MethodCallRenameCollector $methodCallRenameCollector
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
        $decoder = new Decoder();
        $neonNode = $decoder->parseToNode($content);

        $isInServicesScope = false;

        $nodeVisitor = function (Node $neonNode) use (&$isInServicesScope) {
            if ($isInServicesScope) {
                if ($neonNode instanceof ArrayNode) {
                    foreach ($neonNode->getSubNodes() as $serviceNode) {
                        // iterate single service
                        $serviceType = $this->resolveServiceType($serviceNode);

                        // could not match the method name
                        if (! is_string($serviceType)) {
                            return null;
                        }

                        $methodCallRenames = $this->findMethodCallRenamesByClass($serviceType);
                        if ($methodCallRenames === []) {
                            return null;
                        }

                        $this->renameMethodCalls($methodCallRenames, $serviceNode);
                    }
                }
            }

            if ($neonNode instanceof LiteralNode) {
                if ($neonNode->toValue() === self::SERVICES_KEYWORD) {
                    $isInServicesScope = true;
                }
            }
        };

        // @see https://forum.nette.org/en/34804-neon-with-ast-parser-and-format-preserving-printing

        $traverser = new Traverser();
        $neonNode = $traverser->traverse($neonNode, $nodeVisitor);

        $refactoredContent = $neonNode->toString();

        // @todo how to preserve spaces?
        $refactoredContent = Strings::replace($refactoredContent, '#\t#', '    ');

        // replace quotes
        return Strings::replace($refactoredContent, '#\"#', '\'');
    }

    private function resolveServiceType(Node $serviceNode): string|null
    {
        if (! $serviceNode instanceof ArrayItemNode) {
            return null;
        }

        if (! $serviceNode->value instanceof ArrayNode) {
            return null;
        }

        foreach ($serviceNode->value->items as $serviceConfigurationItem) {
            if ($serviceConfigurationItem->key === null) {
                continue;
            }

            if ($serviceConfigurationItem->key->toString() === 'factory') {
                if ($serviceConfigurationItem->value instanceof EntityNode) {
                    return $serviceConfigurationItem->value->value->toString();
                }

                return $serviceConfigurationItem->value->toString();
            }

            if ($serviceConfigurationItem->key->toString() === 'class') {
                if ($serviceConfigurationItem->value instanceof EntityNode) {
                    return $serviceConfigurationItem->value->value->toString();
                }

                return $serviceConfigurationItem->value->toString();
            }
        }

        return null;
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

    /**
     * @param MethodCallRenameInterface[] $methodCallRenames
     */
    private function renameMethodCalls(array $methodCallRenames, Node $serviceNode): Node|null
    {
        if (! $serviceNode instanceof ArrayItemNode) {
            return null;
        }

        if (! $serviceNode->value instanceof ArrayNode) {
            return null;
        }

        foreach ($serviceNode->value->items as $arrayItem) {
            if ($arrayItem->key === null) {
                continue;
            }
            // method calls
            if ($arrayItem->key->toString() !== 'setup') {
                continue;
            }

            if ($arrayItem->value instanceof ArrayNode) {
                foreach ($arrayItem->value->items as $callArrayItem) {
                    if ($callArrayItem->value instanceof EntityNode) {
                        $methodName = $callArrayItem->value->value->toString();

                        foreach ($methodCallRenames as $methodCallRename) {
                            if ($methodCallRename->getOldMethod() !== $methodName) {
                                continue;
                            }

                            // rename method happens here
                            $callArrayItem->value->value = new LiteralNode($methodCallRename->getNewMethod());
                        }
                    }
                }
            }
        }

        return $serviceNode;
    }
}
