<?php

declare(strict_types=1);

namespace Rector\Nette\Contract\Rector;

use Rector\Core\Contract\Rector\RectorInterface;

interface LatteRectorInterface extends RectorInterface
{
    public function changeContent(string $content): string;
}
