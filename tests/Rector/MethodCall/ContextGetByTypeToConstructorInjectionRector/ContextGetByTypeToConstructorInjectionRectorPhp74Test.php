<?php

declare(strict_types=1);

namespace RectorNette\Tests\Rector\MethodCall\ContextGetByTypeToConstructorInjectionRector;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class ContextGetByTypeToConstructorInjectionRectorPhp74Test extends AbstractRectorTestCase
{
    /**
     * @requires PHP 7.4
     * @dataProvider provideData()
     */
    public function test(string $file): void
    {
        $this->doTestFile($file);
    }

    /**
     * @return Iterator<string>
     */
    public function provideData(): Iterator
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/FixturePhp74');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }
}
