<?php

namespace Rector\Nette\Tests\NonPhpFile\NetteDINeonMethodCallRenamer\Source;

final class FirstService implements ServiceInterface
{
    /** @var array<string, string> */
    private $config = [];

    public function add(string $key, string $value): void
    {
        $this->config[$key] = $value;
    }
}
