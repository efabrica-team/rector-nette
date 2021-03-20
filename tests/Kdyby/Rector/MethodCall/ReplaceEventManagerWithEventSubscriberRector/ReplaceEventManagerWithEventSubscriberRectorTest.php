<?php

declare(strict_types=1);

namespace Rector\Nette\Tests\Kdyby\Rector\MethodCall\ReplaceEventManagerWithEventSubscriberRector;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ReplaceEventManagerWithEventSubscriberRectorTest extends AbstractRectorTestCase
{
    public function test(): void
    {
        $fixtureFileInfo = new SmartFileInfo(__DIR__ . '/Fixture/fixture.php.inc');
        $this->doTestFileInfo($fixtureFileInfo);
        $this->doTestExtraFile('Event/SomeClassCopyEvent.php', __DIR__ . '/Source/ExpectedSomeClassCopyEvent.php');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }
}
