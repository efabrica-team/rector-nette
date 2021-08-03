<?php

declare(strict_types=1);

namespace Rector\Nette\Tests\Rector\Class_\FormDataRector;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Rector\Nette\Rector\Class_\FormDataRector
 */
final class FormDataRectorTest extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        [$originalPhpContent, $expectedPhpContent, $formDataClassPath, $formDataClassOriginalContent, $formDataClassExpectedContent] = explode(
            '-----',
            $fileInfo->getContents()
        );

        $fixturePath = $this->getFixtureTempDirectory() . '/' . $fileInfo->getFilename();
        $this->createFixtureDir($fixturePath);
        file_put_contents($fixturePath, $originalPhpContent . '-----' . $expectedPhpContent);

        $fixtureFormDataClassPath = $this->getFixtureTempDirectory() . '/' . trim($formDataClassPath);
        if ($formDataClassOriginalContent) {
            $this->createFixtureDir($fixtureFormDataClassPath);
            $formDataClassOriginalContent = trim($formDataClassOriginalContent);
            file_put_contents($fixtureFormDataClassPath, $formDataClassOriginalContent);
        }

        $newFileInfo = new SmartFileInfo($fixturePath);
        $this->doTestFileInfo($newFileInfo);

        $addedFilesWithContent = $this->removedAndAddedFilesCollector->getAddedFilesWithContent();
        $updatedTemplate = $addedFilesWithContent[0];

        $this->assertSame($fixtureFormDataClassPath, $updatedTemplate->getFilePath());
        $formDataClassExpectedContent = trim($formDataClassExpectedContent) . "\n";
        $this->assertSame($formDataClassExpectedContent, $updatedTemplate->getFileContent());

        unlink($fixturePath);
        unlink($fixtureFormDataClassPath);
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
