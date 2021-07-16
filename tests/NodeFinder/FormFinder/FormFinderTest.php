<?php

declare(strict_types=1);

namespace Rector\Nette\Tests\NodeFinder\FormFinder;

use Iterator;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Nette\NodeFinder\FormFinder;
use Rector\Testing\PHPUnit\AbstractTestCase;
use Rector\Testing\TestingParser\TestingParser;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Rector\Nette\NodeFinder\FormFinder
 */
final class FormFinderTest extends AbstractTestCase
{
    private FormFinder $formFinder;

    private TestingParser $parser;

    protected function setUp(): void
    {
        $this->bootFromConfigFileInfos([new SmartFileInfo(__DIR__ . '/../../../config/config.php')]);
        $this->formFinder = $this->getService(FormFinder::class);
        $this->parser = $this->getService(TestingParser::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $fixtureContent = $fixtureFileInfo->getContents();
        [$content, $expected] = explode("-----\n", trim($fixtureContent), 2);

        $tmpFilePath = sys_get_temp_dir() . '/' . uniqid() . '.php';
        file_put_contents($tmpFilePath, $content);
        $nodes = $this->parser->parseFileToDecoratedNodes($tmpFilePath);
        unlink($tmpFilePath);

        $classNode = $this->findClassNode($nodes);
        if ($classNode === null) {
            throw new ShouldNotHappenException('No class node found');
        }

        $form = $this->formFinder->findFormVariable($classNode);
        if ($form === null) {
            throw new ShouldNotHappenException('No form variable found');
        }

        $fields = $this->formFinder->findFormFields($classNode, $form);
        $output = json_encode($fields, JSON_PRETTY_PRINT);
        $this->assertSame($expected, $output);
    }

    /**
     * @return Iterator<SmartFileInfo>
     */
    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectoryExclusively(__DIR__ . '/Fixture');
    }

    /**
     * @param Node[] $nodes
     */
    private function findClassNode(array $nodes): ?Class_
    {
        foreach ($nodes as $node) {
            if ($node instanceof Namespace_) {
                return $this->findClassNode($node->stmts);
            }
            if ($node instanceof Class_) {
                return $node;
            }
        }
        return null;
    }
}
