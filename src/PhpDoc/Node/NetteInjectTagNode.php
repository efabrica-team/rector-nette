<?php

declare(strict_types=1);

namespace Rector\Nette\PhpDoc\Node;

use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;

use Rector\PhpAttribute\Contract\PhpAttributableTagNodeInterface;

/**
 * @see \Rector\BetterPhpDocParser\PhpDocNodeFactory\StringMatchingPhpDocNodeFactory\NetteInjectPhpDocNodeFactory
 */
final class NetteInjectTagNode extends PhpDocTagNode implements PhpAttributableTagNodeInterface
{
    /**
     * @var string
     */
    public const NAME = '@inject';

    public function __construct()
    {
        parent::__construct(self::NAME, new GenericTagValueNode(''));
    }

    public function getShortName(): string
    {
        return self::NAME;
    }

    public function getAttributeClassName(): string
    {
        return 'Nette\DI\Attributes\Inject';
    }

    /**
     * @return mixed[]
     */
    public function getAttributableItems(): array
    {
        return [];
    }
}