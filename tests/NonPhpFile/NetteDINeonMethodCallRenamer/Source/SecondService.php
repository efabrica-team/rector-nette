<?php

namespace Rector\Nette\Tests\NonPhpFile\NetteDINeonMethodCallRenamer\Source;

final class SecondService implements ServiceInterface
{
    /** @var string */
    private $name;

    /** @var array<string, string> */
    private $config = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function add(string $key, string $value): void
    {
        $this->config[$key] = $value;
    }
}
