<?php

declare(strict_types=1);

namespace Rector\Nette\Kdyby\DataProvider;

use PhpParser\Node\Stmt\ClassMethod;
use Rector\NodeCollector\NodeCollector\NodeRepository;

final class GetSubscribedEventsClassMethodProvider
{
    public function __construct(
        private NodeRepository $nodeRepository
    ) {
    }

    /**
     * @return ClassMethod[]
     */
    public function provide(): array
    {
        $subscriberClasses = $this->nodeRepository->findClassesAndInterfacesByType('Kdyby\Events\Subscriber');
        $classMethods = [];

        foreach ($subscriberClasses as $subscriberClass) {
            $subscribedEventsClassMethod = $subscriberClass->getMethod('getSubscribedEvents');
            if ($subscribedEventsClassMethod === null) {
                continue;
            }

            $classMethods[] = $subscribedEventsClassMethod;
        }

        return $classMethods;
    }
}
