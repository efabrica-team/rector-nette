<?php

declare(strict_types=1);

namespace Rector\Nette\Kdyby\NodeAnalyzer;

use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Type\ObjectType;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\NodeTypeResolver\NodeTypeResolver;

final class GetSubscribedEventsClassMethodAnalyzer
{
    public function __construct(
        private NodeTypeResolver $nodeTypeResolver,
        private NodeNameResolver $nodeNameResolver
    ) {
    }

    public function detect(ClassMethod $classMethod): bool
    {
        $classLike = $classMethod->getAttribute(AttributeKey::CLASS_NODE);
        if (! $classLike instanceof ClassLike) {
            return false;
        }

        if (! $this->nodeTypeResolver->isObjectType($classLike, new ObjectType('Kdyby\Events\Subscriber'))) {
            return false;
        }

        return $this->nodeNameResolver->isName($classMethod, 'getSubscribedEvents');
    }
}
