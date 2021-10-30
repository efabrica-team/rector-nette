<?php

declare(strict_types=1);

namespace Rector\Nette\NeonParser;

use Nette\Neon\Decoder;
use Nette\Neon\Node;

final class NeonParser
{
    public function __construct(
        private Decoder $decoder
    ) {
    }

    public function parseString(string $neonContent): Node
    {
        return $this->decoder->parseToNode($neonContent);
    }

}
