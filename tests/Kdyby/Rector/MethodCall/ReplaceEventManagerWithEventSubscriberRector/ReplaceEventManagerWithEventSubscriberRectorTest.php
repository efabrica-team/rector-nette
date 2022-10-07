<?php

declare(strict_types=1);

namespace RectorNette\Tests\Kdyby\Rector\MethodCall\ReplaceEventManagerWithEventSubscriberRector;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class ReplaceEventManagerWithEventSubscriberRectorTest extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideData()
     * @return never
     */
    public function test(string $file)
    {
        $this->markTestSkipped('Without this test, there is some Comment autoload issue');
    }

    /**
     * @return Iterator<string>
     */
    public function provideData(): Iterator
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }
}
