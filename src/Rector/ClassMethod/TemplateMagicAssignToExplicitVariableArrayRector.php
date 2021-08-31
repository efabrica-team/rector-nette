<?php

declare(strict_types=1);

namespace Rector\Nette\Rector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use Rector\Core\Rector\AbstractRector;
use Rector\Nette\NodeAnalyzer\ConditionalTemplateAssignReplacer;
use Rector\Nette\NodeAnalyzer\MethodCallArgMerger;
use Rector\Nette\NodeAnalyzer\NetteClassAnalyzer;
use Rector\Nette\NodeAnalyzer\RenderMethodAnalyzer;
use Rector\Nette\NodeAnalyzer\TemplatePropertyAssignCollector;
use Rector\Nette\NodeAnalyzer\TemplatePropertyParametersReplacer;
use Rector\Nette\NodeFactory\RenderParameterArrayFactory;
use Rector\Nette\ValueObject\TemplateParametersAssigns;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Rector\Nette\Tests\Rector\ClassMethod\TemplateMagicAssignToExplicitVariableArrayRector\TemplateMagicAssignToExplicitVariableArrayRectorTest
 */
final class TemplateMagicAssignToExplicitVariableArrayRector extends AbstractRector
{
    public function __construct(
        private TemplatePropertyAssignCollector $templatePropertyAssignCollector,
        private RenderMethodAnalyzer $renderMethodAnalyzer,
        private NetteClassAnalyzer $netteClassAnalyzer,
        private RenderParameterArrayFactory $renderParameterArrayFactory,
        private ConditionalTemplateAssignReplacer $conditionalTemplateAssignReplacer,
        private TemplatePropertyParametersReplacer $templatePropertyParametersReplacer,
        private MethodCallArgMerger $methodCallArgMerger
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Change `$this->templates->{magic}` to `$this->template->render(..., $values)` in components',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Nette\Application\UI\Control;

class SomeControl extends Control
{
    public function render()
    {
        $this->template->param = 'some value';
        $this->template->render(__DIR__ . '/poll.latte');
    }
}
CODE_SAMPLE
,
                    <<<'CODE_SAMPLE'
use Nette\Application\UI\Control;

class SomeControl extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/poll.latte', ['param' => 'some value']);
    }
}
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
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->shouldSkip($node)) {
            return null;
        }

        $renderMethodCalls = $this->renderMethodAnalyzer->machRenderMethodCalls($node);
        if ($renderMethodCalls === []) {
            return null;
        }

        if (! $this->haveMethodCallsFirstArgument($renderMethodCalls)) {
            return null;
        }

        // initialize $parameters variable for multiple renders
        if (count($renderMethodCalls) > 1) {
            return $this->refactorForMultipleRenderMethodCalls($node, $renderMethodCalls);
        }

        return $this->refactorForSingleRenderMethodCall($node, $renderMethodCalls[0]);
    }

    private function shouldSkip(ClassMethod $classMethod): bool
    {
        if (! $classMethod->isPublic()) {
            return true;
        }

        if (! $this->isNames($classMethod, ['render', 'render*'])) {
            return true;
        }

        return ! $this->netteClassAnalyzer->isInComponent($classMethod);
    }

    /**
     * @param MethodCall[] $methodCalls
     */
    private function haveMethodCallsFirstArgument(array $methodCalls): bool
    {
        foreach ($methodCalls as $methodCall) {
            if (! isset($methodCall->args[0])) {
                return false;
            }
        }

        return true;
    }

    private function refactorForSingleRenderMethodCall(
        ClassMethod $classMethod,
        MethodCall $renderMethodCall
    ): ?ClassMethod {
        $templateParametersAssigns = $this->templatePropertyAssignCollector->collect($classMethod);

        $array = $this->renderParameterArrayFactory->createArray($templateParametersAssigns);
        if (! $array instanceof Array_) {
            return null;
        }

<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
>>>>>>> 49b7d68 (fixup! cleanup)
=======
>>>>>>> 5ef15d5 (fixup! cleanup)
        $this->traverseNodesWithCallable($classMethod, function (Node $node) use ($templateParametersAssigns) {
            if (! $node instanceof Assign) {
                return null;
            }

            foreach ($templateParametersAssigns->getTemplateParameterAssigns() as $alwaysTemplateParameterAssign) {
                if ($this->nodeComparator->areNodesEqual($node->var, $alwaysTemplateParameterAssign->getAssignVar())) {
                    $this->removeNode($node);
                    return null;
                }
            }

            return $this->replaceThisTemplateAssignWithVariable($templateParametersAssigns, $node);
        });

<<<<<<< HEAD
<<<<<<< HEAD
>>>>>>> 00da73e (fixup! fixup! cleanup)
=======
>>>>>>> 49b7d68 (fixup! cleanup)
=======
>>>>>>> 5ef15d5 (fixup! cleanup)
        $this->conditionalTemplateAssignReplacer->processClassMethod($templateParametersAssigns);

        // has already an array?
        $this->methodCallArgMerger->mergeOrApendArray($renderMethodCall, 1, $array);

        foreach ($templateParametersAssigns->getTemplateParameterAssigns() as $alwaysTemplateParameterAssign) {
            $this->removeNode($alwaysTemplateParameterAssign->getAssign());
        }

        $this->rightAssignTemplateRemover->removeInClassMethod($classMethod);

        return $classMethod;
    }

    /**
     * @param MethodCall[] $renderMethodCalls
     */
    private function refactorForMultipleRenderMethodCalls(
        ClassMethod $classMethod,
        array $renderMethodCalls
    ): ?ClassMethod {
        $magicTemplateParametersAssigns = $this->templatePropertyAssignCollector->collect($classMethod);

        if ($magicTemplateParametersAssigns->getTemplateParameterAssigns() === []) {
            return null;
        }

        $parametersVariable = new Variable('parameters');
        $parametersAssign = new Assign($parametersVariable, new Array_());
        $assignExpression = new Expression($parametersAssign);
        $classMethod->stmts = array_merge([$assignExpression], (array) $classMethod->stmts);

        $this->templatePropertyParametersReplacer->replace($magicTemplateParametersAssigns, $parametersVariable);

        foreach ($renderMethodCalls as $renderMethodCall) {
            $renderMethodCall->args[1] = new Arg($parametersVariable);
        }

        return $classMethod;
    }

    private function replaceThisTemplateAssignWithVariable(
        TemplateParametersAssigns $templateParametersAssigns,
        Assign $assign
    ): null|Assign {
        foreach ($templateParametersAssigns->getDefaultChangeableTemplateParameterAssigns() as $alwaysTemplateParameterAssign) {
            if (! $this->nodeComparator->areNodesEqual($assign->var, $alwaysTemplateParameterAssign->getAssignVar())) {
                continue;
            }

            $assign->var = new Variable($alwaysTemplateParameterAssign->getParameterName());
            return $assign;
        }

        return null;
    }
}
