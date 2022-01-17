<?php

declare(strict_types=1);

namespace Rector\Nette\Kdyby\NodeAnalyzer;

use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Type\ObjectType;
use Rector\Core\PhpParser\Node\BetterNodeFinder;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;

final class GetSubscribedEventsClassMethodAnalyzer
{
    public function __construct(
        private readonly NodeTypeResolver $nodeTypeResolver,
        private readonly NodeNameResolver $nodeNameResolver,
        private readonly BetterNodeFinder $betterNodeFinder,
    ) {
    }

    public function detect(ClassMethod $classMethod): bool
    {
        $classLike = $this->betterNodeFinder->findParentType($classMethod, ClassLike::class);
        if (! $classLike instanceof ClassLike) {
            return false;
        }

        if (! $this->nodeTypeResolver->isObjectType($classLike, new ObjectType('Kdyby\Events\Subscriber'))) {
            return false;
        }

        return $this->nodeNameResolver->isName($classMethod, 'getSubscribedEvents');
    }
}
