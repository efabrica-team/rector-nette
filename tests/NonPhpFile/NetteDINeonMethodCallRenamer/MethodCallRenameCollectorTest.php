<?php

namespace Rector\Nette\Tests\NonPhpFile\NetteDINeonMethodCallRenamer;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class MethodCallRenameCollectorTest extends AbstractRectorTestCase
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
        return __DIR__ . '/config/method_call_rename_collector_configured_rule.php';
    }
}
