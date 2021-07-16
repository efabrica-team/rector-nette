<?php

declare(strict_types=1);

namespace Rector\Nette\NodeAnalyzer;

use PhpParser\Node;
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
    /**
     * @var array<class-string<\PhpParser\Node>>
     */
    private const NODE_TYPES = ControlStructure::CONDITIONAL_NODE_SCOPE_TYPES + [FunctionLike::class];

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

    /**
     * @return Node[]
     */
    private function getFoundParents(PropertyFetch $propertyFetch): array
    {
        $foundParents = [];

        /** @var class-string<Node> $nodeType */
        foreach (self::NODE_TYPES as $nodeType) {
            $parentType = $this->betterNodeFinder->findParentType($propertyFetch->var, $nodeType);
            if ($parentType instanceof Node) {
                $foundParents[] = $parentType;
            }
        }

        return $foundParents;
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
        $foundParents = $this->getFoundParents($propertyFetch);

        foreach ($foundParents as $foundParent) {
            if ($this->scopeNestingComparator->isInBothIfElseBranch($foundParent, $propertyFetch)) {
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
