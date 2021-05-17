<?php

declare(strict_types=1);

namespace Rector\Nette\Rector\Latte;

use Nette\Utils\Strings;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Nette\Contract\Rector\LatteRectorInterface;
use Rector\Renaming\Collector\MethodCallRenameCollector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see \Rector\Nette\Tests\Rector\Latte\RenameMethodLatteRector\RenameMethodLatteRectorTest
 */
final class RenameMethodLatteRector implements LatteRectorInterface, ConfigurableRectorInterface
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
        return new RuleDefinition('Renames method calls in LATTE templates', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
{varType SomeClass $someClass}

<div n:foreach="$someClass->oldCall() as $item"></div>
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
{varType SomeClass $someClass}

<div n:foreach="$someClass->newCall() as $item"></div>
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
        $methodCallRenames = array_merge(
            $this->methodCallRenameCollector->getMethodCallRenames(),
            $this->methodCallRenames
        );
        foreach ($methodCallRenames as $methodCallRename) {
            $oldObjectType = $methodCallRename->getOldObjectType();
            $objectClassName = $oldObjectType->getClassName();
            $className = str_replace('\\', '\\\\', $objectClassName);

            $oldMethodName = $methodCallRename->getOldMethod();
            $newMethodName = $methodCallRename->getNewMethod();

            $varTypePattern = '#{varType ' . $className . ' (.*?)}#';
            $varTypeMatches = Strings::matchAll($content, $varTypePattern, PREG_PATTERN_ORDER);

            foreach ($varTypeMatches[1] ?? [] as $classVariableName) {
                $methodCallPattern = '#\\' . $classVariableName . '->' . $oldMethodName . '\(#';
                if (Strings::match($content, $methodCallPattern)) {
                    $content = str_replace(
                        $classVariableName . '->' . $oldMethodName . '(',
                        $classVariableName . '->' . $newMethodName . '(',
                        $content
                    );
                }
            }
        }

        return $content;
    }
}
