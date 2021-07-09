<?php

declare(strict_types=1);

namespace Rector\Nette\NodeFactory;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use Symplify\Astral\ValueObject\NodeBuilder\ClassBuilder;
use Symplify\Astral\ValueObject\NodeBuilder\NamespaceBuilder;
use Symplify\Astral\ValueObject\NodeBuilder\PropertyBuilder;
use Symplify\Astral\ValueObject\NodeBuilder\TraitUseBuilder;


/**
 * @TODO tests
 */
final class ClassWithPublicPropertiesFactory
{
    /**
     * @param string $fullyQualifiedName fully qualified name of new class
     * @param array<string, string|Name|NullableType|Identifier> $properties name => type of property
     * @param string|null $parent fully qualified name of parent class
     * @param string[] $traits list of fully qualified names of traits used in class
     * @return Node
     */
    public function createNode(string $fullyQualifiedName, array $properties, ?string $parent = null, array $traits = []): Node
    {
        $namespaceParts = explode('\\', $fullyQualifiedName);
        $className = array_pop($namespaceParts);
        $namespace = implode('\\', $namespaceParts);

        if ($namespace) {
            $namespaceBuilder = new NamespaceBuilder($namespace);
        }
        $classBuilder = new ClassBuilder($className);
        if ($parent) {
            $classBuilder->extend($parent);
        }

        foreach ($traits as $trait) {
            $classBuilder->addStmt(new TraitUseBuilder($trait));
        }

        foreach ($properties as $propertyName => $propertyType) {
            $classBuilder->addStmt((new PropertyBuilder($propertyName))->setType($propertyType));
        }

        if ($namespace) {
            $namespaceBuilder->addStmt($classBuilder);
            return $namespaceBuilder->getNode();
        }

        return $classBuilder->getNode();
    }
}
