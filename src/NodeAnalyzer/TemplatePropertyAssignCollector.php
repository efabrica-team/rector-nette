<?php

declare(strict_types=1);

namespace Rector\Nette\NodeAnalyzer;

use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use Rector\Core\PhpParser\Node\BetterNodeFinder;
use Rector\Nette\ValueObject\AlwaysTemplateParameterAssign;
use Rector\Nette\ValueObject\ConditionalTemplateParameterAssign;
use Rector\Nette\ValueObject\TemplateParametersAssigns;
use Rector\NodeNestingScope\ScopeNestingComparator;
use Rector\NodeNestingScope\ValueObject\ControlStructure;

final class TemplatePropertyAssignCollector
{
    private ?Return_ $lastReturn = null;

    /**
     * @var AlwaysTemplateParameterAssign[]
     */
    private array $alwaysTemplateParameterAssigns = [];

    /**
     * @var ConditionalTemplateParameterAssign[]
     */
    private array $conditionalTemplateParameterAssigns = [];

    public function __construct(
        private ScopeNestingComparator $scopeNestingComparator,
        private BetterNodeFinder $betterNodeFinder,
        private ThisTemplatePropertyFetchAnalyzer $thisTemplatePropertyFetchAnalyzer,
        private ReturnAnalyzer $returnAnalyzer
    ) {
    }

    public function collect(ClassMethod $classMethod): TemplateParametersAssigns
    {
        $this->alwaysTemplateParameterAssigns = [];
        $this->conditionalTemplateParameterAssigns = [];

        $this->lastReturn = $this->returnAnalyzer->findLastClassMethodReturn($classMethod);

        /** @var Assign[] $assigns */
        $assigns = $this->betterNodeFinder->findInstanceOf((array) $classMethod->stmts, Assign::class);
        foreach ($assigns as $assign) {
            $this->collectVariableFromAssign($assign);
        }

        return new TemplateParametersAssigns(
            $this->alwaysTemplateParameterAssigns,
            $this->conditionalTemplateParameterAssigns
        );
    }

    private function collectVariableFromAssign(Assign $assign): void
    {
        if (! $assign->var instanceof PropertyFetch) {
            return;
        }

        $parameterName = $this->thisTemplatePropertyFetchAnalyzer->resolveTemplateParameterNameFromAssign($assign);
        if ($parameterName === null) {
            return;
        }

        $propertyFetch = $assign->var;

        /** @var array<class-string<\PhpParser\Node>> $nodeTypes */
        $nodeTypes = ControlStructure::CONDITIONAL_NODE_SCOPE_TYPES + [FunctionLike::class];
        $foundParent = $this->betterNodeFinder->findParentTypes($propertyFetch->var, $nodeTypes);

        if ($foundParent && $this->scopeNestingComparator->isInBothIfElseBranch($foundParent, $propertyFetch)) {
            $this->conditionalTemplateParameterAssigns[] = new ConditionalTemplateParameterAssign(
                $assign,
                $parameterName
            );
            return;
        }

        if ($foundParent instanceof If_) {
            return;
        }

        if ($foundParent instanceof Else_) {
            return;
        }

        // there is a return before this assign, to do not remove it and keep ti
        if (! $this->returnAnalyzer->isBeforeLastReturn($assign, $this->lastReturn)) {
            return;
        }

        $this->alwaysTemplateParameterAssigns[] = new AlwaysTemplateParameterAssign(
            $assign,
            $parameterName,
            $assign->expr
        );
    }
}
