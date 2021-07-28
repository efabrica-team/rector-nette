<?php

declare(strict_types=1);

namespace Rector\Nette\Tests\Rector\Class_\LatteVarTypesBasedOnPresenterTemplateParametersRector;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Rector\Nette\Rector\Class_\LatteVarTypesBasedOnPresenterTemplateParametersRector
 */
final class LatteVarTypesBasedOnPresenterTemplateParametersRectorTest extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        [$originalPhpContent, $expectedPhpContent, $lattePath, $originalLatteContent, $expectedLatteContent] = explode(
            '-----',
            $fileInfo->getContents()
        );

        $fixturePath = $this->getFixtureTempDirectory() . '/' . $fileInfo->getFilename();
        $this->createFixtureDir($fixturePath);
        file_put_contents($fixturePath, $originalPhpContent . '-----' . $expectedPhpContent);

        $fixtureLattePath = $this->getFixtureTempDirectory() . '/' . trim($lattePath);
        $this->createFixtureDir($fixtureLattePath);
        $originalLatteContent = trim($originalLatteContent);
        file_put_contents($fixtureLattePath, $originalLatteContent);

        $newFileInfo = new SmartFileInfo($fixturePath);
        $this->doTestFileInfo($newFileInfo);

        $addedFilesWithContent = $this->removedAndAddedFilesCollector->getAddedFilesWithContent();
        $updatedTemplate = $addedFilesWithContent[0];

        $this->assertSame($fixtureLattePath, $updatedTemplate->getFilePath());
        $expectedLatteContent = trim($expectedLatteContent);
        $this->assertSame($expectedLatteContent, $updatedTemplate->getFileContent());

        unlink($fixturePath);
        unlink($fixtureLattePath);
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

    private function createFixtureDir(string $fileName): void
    {
        $dirName = dirname($fileName);
        if (! file_exists($dirName)) {
            mkdir($dirName, 0777, true);
        }
    }
}
