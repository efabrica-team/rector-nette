<?php

namespace Rector\Nette\Tests\Rector\Latte\RenameMethodLatteRector;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class RenameMethodLatteRectorWithSharedMethodCallRenameCollectorTest extends AbstractRectorTestCase
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
        return $this->yieldFilesFromDirectory(__DIR__ . '/Fixture', '*.latte');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule_shared_collector.php';
    }
}
