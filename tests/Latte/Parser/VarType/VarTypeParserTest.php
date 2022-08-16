<?php

declare(strict_types=1);

namespace RectorNette\Tests\Latte\Parser\VarType;

use Iterator;
use Rector\Testing\PHPUnit\AbstractTestCase;
use RectorNette\Latte\Parser\VarTypeParser;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

final class VarTypeParserTest extends AbstractTestCase
{
    private VarTypeParser $varTypeParser;

    protected function setUp(): void
    {
        $this->bootFromConfigFiles([__DIR__ . '/../../../../config/config.php']);
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
