<?php

declare(strict_types=1);

namespace Rector\Nette\PhpDoc\Node;

use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;

final class NettePersistentTagNode extends PhpDocTagNode
{
    /**
     * @var string
     */
    public const NAME = '@persistent';

    public function __construct()
    {
        parent::__construct(self::NAME, new GenericTagValueNode(''));
    }

    public function getShortName(): string
    {
        return self::NAME;
    }
}
