<?php

declare(strict_types=1);

namespace RectorNette\Contract\Rector;

use Rector\Core\Contract\Rector\RectorInterface;

interface LatteRectorInterface extends RectorInterface
{
    public function changeContent(string $content): string;
}
