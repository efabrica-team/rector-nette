<?php

declare(strict_types=1);

namespace Rector\Nette\NodeAnalyzer;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use PHPStan\Reflection\ClassReflection;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\NodeAnalyzer\PropertyFetchAnalyzer;
use Rector\Core\PhpParser\AstResolver;
use Rector\Core\PhpParser\Node\BetterNodeFinder;
use Rector\Core\Reflection\ReflectionResolver;
use Rector\FamilyTree\Reflection\FamilyRelationsAnalyzer;
use Rector\NodeNameResolver\NodeNameResolver;

final class PropertyUsageAnalyzer
{
    public function __construct(
        private readonly BetterNodeFinder $betterNodeFinder,
        private readonly FamilyRelationsAnalyzer $familyRelationsAnalyzer,
        private readonly NodeNameResolver $nodeNameResolver,
        private readonly AstResolver $astResolver,
        private readonly PropertyFetchAnalyzer $propertyFetchAnalyzer,
        private readonly ReflectionResolver $reflectionResolver
    ) {
    }

    public function isPropertyFetchedInChildClass(Property $property): bool
    {
        $classReflection = $this->reflectionResolver->resolveClassReflection($property);
        if (! $classReflection instanceof ClassReflection) {
            throw new ShouldNotHappenException();
        }

        if ($classReflection->isClass() && $classReflection->isFinal()) {
            return false;
        }

        $propertyName = $this->nodeNameResolver->getName($property);

        $childrenClassReflections = $this->familyRelationsAnalyzer->getChildrenOfClassReflection($classReflection);

        foreach ($childrenClassReflections as $childClassReflection) {
            $childClass = $this->astResolver->resolveClassFromName($childClassReflection->getName());
            if (! $childClass instanceof Class_) {
                continue;
            }

            $isPropertyFetched = (bool) $this->betterNodeFinder->findFirst(
                $childClass->stmts,
                fn (Node $node): bool => $this->propertyFetchAnalyzer->isLocalPropertyFetchName($node, $propertyName)
            );

            if ($isPropertyFetched) {
                return true;
            }
        }

        return false;
    }
}
