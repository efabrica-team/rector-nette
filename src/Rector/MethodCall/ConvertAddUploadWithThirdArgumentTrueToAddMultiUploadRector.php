<?php

declare(strict_types=1);

namespace RectorNette\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorNette\Tests\Rector\MethodCall\ConvertAddUploadWithThirdArgumentTrueToAddMultiUploadRector\ConvertAddUploadWithThirdArgumentTrueToAddMultiUploadRectorTest
 */
final class ConvertAddUploadWithThirdArgumentTrueToAddMultiUploadRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'convert addUpload() with 3rd argument true to addMultiUpload()',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
$form = new Nette\Forms\Form();
$form->addUpload('...', '...', true);
CODE_SAMPLE

                    ,
                    <<<'CODE_SAMPLE'
$form = new Nette\Forms\Form();
$form->addMultiUpload('...', '...');
CODE_SAMPLE
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isObjectType($node->var, new ObjectType('Nette\Forms\Form'))) {
            return null;
        }

        if (! $this->isName($node->name, 'addUpload')) {
            return null;
        }

        $args = $node->getArgs();
        if (! isset($args[2])) {
            return null;
        }

        $thirdArg = $args[2];

        if ($this->valueResolver->isTrue($thirdArg->value)) {
            $node->name = new Identifier('addMultiUpload');
            unset($node->args[2]);
            return $node;
        }

        return null;
    }
}
