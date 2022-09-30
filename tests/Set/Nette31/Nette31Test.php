<?php

declare(strict_types=1);

namespace RectorNette\Tests\Set\Nette31;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class Nette31Test // extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(string $file): void
    {
        $this->markTestSkipped('Rector\Composer classes were removed from rector core');

        $this->doTestFile($file);
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
        return __DIR__ . '/config/nette31_attributes.php';
    }
}
