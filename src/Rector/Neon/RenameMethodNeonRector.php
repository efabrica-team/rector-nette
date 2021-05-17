<?php

declare(strict_types=1);

namespace Rector\Nette\Rector\Neon;

use Nette\Utils\Strings;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Nette\Contract\Rector\NeonRectorInterface;
use Rector\Renaming\Collector\MethodCallRenameCollector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see \Rector\Nette\Tests\Rector\Neon\RenameMethodNeonRector\RenameMethodNeonRectorTest
 */
final class RenameMethodNeonRector implements NeonRectorInterface, ConfigurableRectorInterface
{
    /**
     * @var string
     */
    public const RENAME_METHODS = 'rename_methods';

    /**
     * @var MethodCallRename[]
     */
    private array $methodCallRenames = [];

    public function __construct(
        private MethodCallRenameCollector $methodCallRenameCollector
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Renames method calls in NEON configs', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
services:
    -
        class: SomeClass
        setup:
            - oldCall
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
services:
    -
        class: SomeClass
        setup:
            - newCall
CODE_SAMPLE
                ,
                [
                    self::RENAME_METHODS => [new MethodCallRename('SomeClass', 'oldCall', 'newCall')],
                ]
            ),
        ]);
    }

    /**
     * @param array<string, MethodCallRename[]> $configuration
     */
    public function configure(array $configuration): void
    {
        $methodCallRenames = $configuration[self::RENAME_METHODS] ?? [];
        Assert::allIsInstanceOf($methodCallRenames, MethodCallRename::class);
        $this->methodCallRenames = $methodCallRenames;
    }

    public function changeContent(string $content): string
    {
        $methodCallRenames = array_merge($this->methodCallRenameCollector->getMethodCallRenames(), $this->methodCallRenames);
        foreach ($methodCallRenames as $methodCallRename) {
            $oldObjectType = $methodCallRename->getOldObjectType();
            $objectClassName = $oldObjectType->getClassName();
            $className = str_replace('\\', '\\\\', $objectClassName);

            $oldMethodName = $methodCallRename->getOldMethod();
            $newMethodName = $methodCallRename->getNewMethod();

            $pattern = '#\n(.*?)(class|factory): ' . $className . '(\n|\((.*?)\)\n)\1setup:(.*?)- ' . $oldMethodName . '\(#s';
            if (Strings::match($content, $pattern)) {
                $content = str_replace($oldMethodName . '(', $newMethodName . '(', $content);
            }
        }

        return $content;
    }
}
