<?php

namespace Rector\Nette\NeonParser\Contract;

use Nette\Neon\Node;

interface NeonNodeVisitorInterface
{
    public function enterNode(Node $node);
}
