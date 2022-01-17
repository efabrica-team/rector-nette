<?php

declare(strict_types=1);

namespace Rector\Nette\Kdyby\NodeManipulator;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Name\FullyQualified;
use Rector\Core\PhpParser\Node\Value\ValueResolver;
use Rector\Nette\Kdyby\ValueObject\NetteEventToContributeEventClass;
use Symplify\Astral\NodeTraverser\SimpleCallableNodeTraverser;

final class GetSubscribedEventsArrayManipulator
{
    public function __construct(
        private readonly SimpleCallableNodeTraverser $simpleCallableNodeTraverser,
        private readonly ValueResolver $valueResolver
    ) {
    }

    public function change(Array_ $array): void
    {
        $arrayItems = array_filter($array->items, fn (ArrayItem|null $arrayItem): bool => $arrayItem !== null);

        $this->simpleCallableNodeTraverser->traverseNodesWithCallable($arrayItems, function (Node $node): ?Node {
            if (! $node instanceof ArrayItem) {
                return null;
            }

            foreach (NetteEventToContributeEventClass::PROPERTY_TO_EVENT_CLASS as $netteEventProperty => $contributeEventClass) {
                if ($node->key === null) {
                    continue;
                }

                if (! $this->valueResolver->isValue($node->key, $netteEventProperty)) {
                    continue;
                }

                $node->key = new ClassConstFetch(new FullyQualified($contributeEventClass), 'class');
            }

            return $node;
        });
    }
}
