<?php

namespace Rector\Nette\Tests\Rector\Neon\RenameMethodNeonRector;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class RenameMethodNeonRectorWithSharedMethodCallRenameCollectorTest extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        $this->doTestFileInfo($fileInfo);
    }

    /**
     * @return Iterator<SmartFileInfo>
     */
    public function provideData(): Iterator
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/Fixture', '*.neon');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule_shared_collector.php';
    }
}
