<?php

declare(strict_types=1);

namespace Rector\Nette\PhpDoc\NodeFactory;

use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use Rector\BetterPhpDocParser\Contract\StringTagMatchingPhpDocNodeFactoryInterface;
use Rector\Nette\PhpDoc\Node\NettePersistentTagNode;

final class NettePersistentPhpDocNodeFactory implements StringTagMatchingPhpDocNodeFactoryInterface
{
    public function match(string $tag): bool
    {
        return $tag === NettePersistentTagNode::NAME;
    }

    public function createFromTokens(TokenIterator $tokenIterator): ?Node
    {
        return new NettePersistentTagNode();
    }
}
