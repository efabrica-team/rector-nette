<?php

declare(strict_types=1);

namespace Rector\Nette\Tests\NodeFinder\FormFinder;

use PhpParser\Node\Expr\Variable;
use Iterator;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Nette\NodeFinder\FormFieldsFinder;
use Rector\Nette\NodeFinder\FormVariableFinder;
use Rector\Testing\PHPUnit\AbstractTestCase;
use Rector\Testing\TestingParser\TestingParser;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Rector\Nette\NodeFinder\FormVariableFinder
 * @see \Rector\Nette\NodeFinder\FormFieldsFinder
 */
final class FormFinderTest extends AbstractTestCase
{
    private FormVariableFinder $formVariableFinder;

    private FormFieldsFinder $formFieldsFinder;

    private TestingParser $parser;

    protected function setUp(): void
    {
        $this->bootFromConfigFiles([__DIR__ . '/../../../config/config.php']);
        $this->formVariableFinder = $this->getService(FormVariableFinder::class);
        $this->formFieldsFinder = $this->getService(FormFieldsFinder::class);
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
        if (!$classNode instanceof Class_) {
            throw new ShouldNotHappenException('No class node found');
        }

        $form = $this->formVariableFinder->find($classNode);
        if (!$form instanceof Variable) {
            throw new ShouldNotHappenException('No form variable found');
        }

        $fields = $this->formFieldsFinder->find($classNode, $form);

        $output = [];
        foreach ($fields as $field) {
            $output[$field->getName()] = [
                'type' => $field->getType(),
                'required' => $field->isRequired(),
            ];
        }

        $output = json_encode($output, JSON_PRETTY_PRINT);
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
