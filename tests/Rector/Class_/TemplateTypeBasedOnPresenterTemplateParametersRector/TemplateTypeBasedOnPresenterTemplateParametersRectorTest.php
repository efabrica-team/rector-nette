<?php

declare(strict_types=1);

namespace RectorNette\Tests\Rector\Class_\TemplateTypeBasedOnPresenterTemplateParametersRector;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use RectorNette\Tests\Behavior\MultipleFilesChangedTrait;

/**
 * @see \RectorNette\Rector\Class_\TemplateTypeBasedOnPresenterTemplateParametersRector
 */
final class TemplateTypeBasedOnPresenterTemplateParametersRectorTest extends AbstractRectorTestCase
{
    use MultipleFilesChangedTrait;

    /**
     * @dataProvider provideData()
     */
    public function test(string $file): void
    {
        $this->doTestFileInfoWithAdditionalChanges($file);
    }

    /**
     * @return Iterator<string>
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
