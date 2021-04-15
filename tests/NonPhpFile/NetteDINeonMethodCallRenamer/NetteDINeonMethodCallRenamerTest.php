<?php

namespace Rector\Nette\Tests\NonPhpFile\NetteDINeonMethodCallRenamer;

use Iterator;
use Rector\Core\HttpKernel\RectorKernel;
use Rector\Core\ValueObject\Application\File;
use Rector\Nette\NonPhpFile\NetteDINeonMethodCallRenamer;
use Rector\Nette\Tests\NonPhpFile\NetteDINeonMethodCallRenamer\Source\SecondService;
use Rector\Renaming\Collector\MethodCallRenameCollector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class NetteDINeonMethodCallRenamerTest extends AbstractKernelTestCase
{
    /**
     * @var NetteDINeonMethodCallRenamer
     */
    private $netteDINeonMethodCallRenamer;

    /**
     * @var MethodCallRenameCollector
     */
    private $methodCallRenameCollector;

    protected function setUp(): void
    {
        $this->bootKernel(RectorKernel::class);

        $this->netteDINeonMethodCallRenamer = $this->getService(NetteDINeonMethodCallRenamer::class);
        $this->methodCallRenameCollector = $this->getService(MethodCallRenameCollector::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function testNoRenames(SmartFileInfo $fixtureFileInfo): void
    {
        $file = new File($fixtureFileInfo, $fixtureFileInfo->getContents());
        $oldContent = $file->getFileContent();
        $this->netteDINeonMethodCallRenamer->process([$file]);
        $this->assertFalse($file->hasChanged());
        $this->assertEquals($oldContent, $file->getFileContent());
    }

    /**
     * @dataProvider provideData()
     */
    public function testRenameAddToAddConfigForSecondService(SmartFileInfo $fixtureFileInfo): void
    {
        $this->methodCallRenameCollector->addMethodCallRename(
            new MethodCallRename(SecondService::class, 'add', 'addConfig')
        );

        $file = new File($fixtureFileInfo, $fixtureFileInfo->getContents());
        $this->netteDINeonMethodCallRenamer->process([$file]);

        $expected = "services:
    firstService:
        factory: Rector\Core\Tests\NonPhpFile\NetteDINeonMethodCallRenamer\Source\FirstService
        setup:
            - add('key1', 'value1')
            - add('key2', 'value2')

    -
        class: Rector\Core\Tests\NonPhpFile\NetteDINeonMethodCallRenamer\Source\SecondService('service-name')
        setup:
            - addConfig('first-key', 'first-value')
            - addConfig('second-key', 'second-value')
";

        $this->assertTrue($file->hasChanged());
        $this->assertEquals($expected, $file->getFileContent());
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.neon');
    }
}
