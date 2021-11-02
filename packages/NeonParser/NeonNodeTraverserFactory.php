<?php

declare(strict_types=1);

namespace Rector\Nette\NeonParser;

use Rector\Nette\NeonParser\NodeFactory\ServiceFactory;
use Rector\Nette\NeonParser\Services\ServiceTypeResolver;

final class NeonNodeTraverserFactory
{
    public function __construct(
        private ServiceTypeResolver $serviceTypeResolver,
        private ServiceFactory $serviceFactory,
    ) {
    }

    public function create(): NeonNodeTraverser
    {
        return new NeonNodeTraverser($this->serviceTypeResolver, $this->serviceFactory);
    }
}
