<?php

namespace Rector\Nette\Tests\Latte\Parser\VarType;

use Iterator;
use Rector\Nette\Latte\Parser\VarTypeParser;
use Rector\Testing\PHPUnit\AbstractTestCase;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

final class VarTypeParserTest extends AbstractTestCase
{
    private VarTypeParser $varTypeParser;

    protected function setUp(): void
    {
        $this->bootFromConfigFileInfos([new SmartFileInfo(__DIR__ . '/../../../../config/config.php')]);
        $this->varTypeParser = $this->getService(VarTypeParser::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $fixtureContent = $fixtureFileInfo->getContents();
        [$content, $expected] = explode("-----\n", $fixtureContent, 2);

        $output = print_r($this->varTypeParser->parse($content), true);
        $this->assertSame($expected, $output);
    }

    /**
     * @return Iterator<SmartFileInfo>
     */
    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectoryExclusively(__DIR__ . '/Fixture');
    }
}
