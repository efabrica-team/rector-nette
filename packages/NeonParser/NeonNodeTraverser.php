<?php

declare(strict_types=1);

namespace Rector\Nette\NeonParser;

use Nette\Neon\Node;
use Rector\Nette\NeonParser\Contract\NeonNodeVisitorInterface;
use Rector\Nette\NeonParser\Node\Service_;
use Rector\Nette\NeonParser\NodeFactory\ServiceFactory;
use Rector\Nette\NeonParser\Services\ServiceTypeResolver;

/**
 * @see https://forum.nette.org/en/34804-neon-with-ast-parser-and-format-preserving-printing
 */
final class NeonNodeTraverser
{
    /**
     * @var NeonNodeVisitorInterface[]
     */
    private array $neonNodeVisitors = [];

    public function __construct(
        private ServiceTypeResolver $serviceTypeResolver,
        private ServiceFactory $serviceFactory,
    ) {
    }

    public function addNeonNodeVisitor(NeonNodeVisitorInterface $neonNodeVisitor): void
    {
        $this->neonNodeVisitors[] = $neonNodeVisitor;
    }

    public function traverse(Node $node): Node
    {
        foreach ($this->neonNodeVisitors as $neonNodeVisitor) {
            // is service node?
            // iterate single service
            $serviceType = $this->serviceTypeResolver->resolve($node);

            // create virtual node
            if (is_string($serviceType)) {
                $service = $this->serviceFactory->create($node);
                if ($service instanceof Service_) {
                    // enter meta node
                    $node = $service;
                }
            }

            // enter node only in case of matching type
            if (is_a($node, $neonNodeVisitor->getNodeType(), true)) {
                $node = $neonNodeVisitor->enterNode($node);
            }

            // traverse all children
            foreach ($node->getSubNodes() as $subnode) {
                $this->traverse($subnode);
            }
        }

        return $node;
    }
}
