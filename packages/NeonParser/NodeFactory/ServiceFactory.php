<?php

declare(strict_types=1);

namespace Rector\Nette\NeonParser\NodeFactory;

use Nette\Neon\Node\ArrayItemNode;
use Nette\Neon\Node\ArrayNode;
use Nette\Neon\Node\EntityNode;
use Nette\Neon\Node\LiteralNode;
use Rector\Nette\NeonParser\Node\Service_;
use Nette\Neon\Node;
use Rector\Nette\NeonParser\Node\Service_\SetupMethodCall;

final class ServiceFactory
{
    /**
     * @var string
     */
    private const CLASS_KEYWORD = 'class';

    /**
     * @var string
     */
    private const SETUP_KEYWORD = 'setup';

    /**
     * @var string
     */
    private const FACTORY_KEYWORD = 'factory';

    public function create(Node $node): Service_|null
    {
        if (! $node instanceof ArrayItemNode) {
            return null;
        }

        $class = $this->resolveArrayItemByKeyword($node, self::CLASS_KEYWORD);
        $factory = $this->resolveArrayItemByKeyword($node, self::FACTORY_KEYWORD);

        $className = $this->resolveServiceName($class, $factory);
        // resolve later
        if (! is_string($className)) {
            return null;
        }

        $setupMethodCalls = $this->resolveSetupMethodCalls($className, $node);

        return new Service_($className, $class, $factory, $setupMethodCalls);
    }

    private function resolveArrayItemByKeyword(ArrayItemNode $arrayItemNode, string $keyword): LiteralNode|null
    {
        if (! $arrayItemNode->value instanceof ArrayNode) {
            return null;
        }

        $arrayNode = $arrayItemNode->value;
        foreach ($arrayNode->items as $arrayItemNode) {
            if (! $arrayItemNode->key instanceof LiteralNode) {
                continue;
            }

            if ($arrayItemNode->key->toString() !== $keyword) {
                continue;
            }

            if ($arrayItemNode->value instanceof EntityNode) {
                return $arrayItemNode->value->value;
            }

            if ($arrayItemNode->value instanceof LiteralNode) {
                return $arrayItemNode->value;
            }
        }

        return null;
    }

    /**
     * @return SetupMethodCall[]
     */
    private function resolveSetupMethodCalls(string $className, ArrayItemNode $arrayItemNode): array
    {
        if (! $arrayItemNode->value instanceof ArrayNode) {
            return [];
        }

        $setupMethodCalls = [];

        $arrayNode = $arrayItemNode->value;
        foreach ($arrayNode->items as $arrayItemNode) {
            if ($arrayItemNode->key instanceof LiteralNode) {
                if ($arrayItemNode->key->toString() !== self::SETUP_KEYWORD) {
                    continue;
                }

                if (!$arrayItemNode->value instanceof ArrayNode) {
                    continue;
                }

                foreach ($arrayItemNode->value->items as $setupArrayItemNode) {
                    if ($setupArrayItemNode->value instanceof EntityNode) {
                        // probably method call
                        $entityNode = $setupArrayItemNode->value;

                        if ($entityNode->value instanceof LiteralNode) {
                            // not a method call - probably property assign
                            if (str_starts_with($entityNode->value->toString(), '$')) {
                                continue;
                            }

                            $setupMethodCalls[] = new SetupMethodCall($className, $entityNode->value, $entityNode);
                        }
                    }
                }
            }
        }

        return $setupMethodCalls;
    }

    private function resolveServiceName(LiteralNode|null $classLiteralNode, LiteralNode|null $factoryLiteralNode): string|null
    {
        if ($classLiteralNode) {
            return $classLiteralNode->toString();
        }

        if ($factoryLiteralNode) {
            return $factoryLiteralNode->toString();
        }

        return null;
    }
}
