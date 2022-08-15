<?php

declare(strict_types=1);

namespace RectorNette\Tests\Latte\Parser\TemplateType;

use Iterator;
use RectorNette\Latte\Parser\TemplateTypeParser;
use Rector\Testing\PHPUnit\AbstractTestCase;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

final class TemplateTypeParserTest extends AbstractTestCase
{
    private TemplateTypeParser $templateTypeParser;

    protected function setUp(): void
    {
        $this->bootFromConfigFiles([__DIR__ . '/../../../../config/config.php']);
        $this->templateTypeParser = $this->getService(TemplateTypeParser::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $fixtureContent = $fixtureFileInfo->getContents();
        [$content, $expected] = explode("-----\n", $fixtureContent, 2);

        $output = print_r($this->templateTypeParser->parse($content), true);
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
