<?php

declare(strict_types=1);

namespace Rector\Nette\Tests\Kdyby\Rector\MethodCall\ReplaceMagicPropertyEventWithEventClassRector;

use Iterator;
use Rector\FileSystemRector\ValueObject\AddedFileWithContent;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class ReplaceMagicPropertyEventWithEventClassRectorTest extends AbstractRectorTestCase
{
    public function testSkip(): void
    {
        $fixtureFileInfo = new SmartFileInfo(__DIR__ . '/Fixture/skip_on_success_in_control.php.inc');
        $this->doTestFileInfo($fixtureFileInfo);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo, AddedFileWithContent $expectedAddedFileWithContent): void
    {
        $this->doTestFileInfo($fixtureFileInfo);
        $this->assertFileWasAdded($expectedAddedFileWithContent);
    }

    /**
     * @return Iterator<array<SmartFileInfo|AddedFileWithContent>>
     */
    public function provideData(): Iterator
    {
        $smartFileSystem = new SmartFileSystem();

        yield [
            new SmartFileInfo(__DIR__ . '/Fixture/simple_event.php.inc'),
            new AddedFileWithContent(
                $this->getFixtureTempDirectory() . '/Event/FileManagerUploadEvent.php',
                $smartFileSystem->readFile(__DIR__ . '/Source/ExpectedFileManagerUploadEvent.php')
            ),
        ];

        yield [
            new SmartFileInfo(__DIR__ . '/Fixture/duplicated_event_params.php.inc'),
            new AddedFileWithContent(
                $this->getFixtureTempDirectory() . '/Event/DuplicatedEventParamsUploadEvent.php',
                $smartFileSystem->readFile(__DIR__ . '/Source/ExpectedDuplicatedEventParamsUploadEvent.php'),
            ),
        ];
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }
}
