<?php

namespace Rector\Nette\Tests\NonPhpFile\NetteDINeonMethodCallRenamer\Source;

interface ServiceInterface
{
    public function add(string $key, string $value): void;
}
