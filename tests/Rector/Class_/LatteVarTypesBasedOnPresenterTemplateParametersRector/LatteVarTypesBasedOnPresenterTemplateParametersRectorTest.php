<?php

declare(strict_types=1);

namespace RectorNette\Tests\Rector\Class_\LatteVarTypesBasedOnPresenterTemplateParametersRector;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \RectorNette\Rector\Class_\LatteVarTypesBasedOnPresenterTemplateParametersRector
 */
final class LatteVarTypesBasedOnPresenterTemplateParametersRectorTest extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        $this->doTestFileInfoWithAdditionalChanges($fileInfo);
    }

    /**
     * @return Iterator<SmartFileInfo>
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
