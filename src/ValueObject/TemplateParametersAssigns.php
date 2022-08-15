<?php

declare(strict_types=1);

namespace RectorNette\ValueObject;

use PhpParser\Node\Expr;

final class TemplateParametersAssigns
{
    /**
     * @param AlwaysTemplateParameterAssign[] $templateParameterAssigns
     * @param ParameterAssign[] $conditionalTemplateParameterAssign
     * @param AlwaysTemplateParameterAssign[] $defaultChangeableTemplateParameterAssigns
     */
    public function __construct(
        private array $templateParameterAssigns,
        private array $conditionalTemplateParameterAssign,
        private array $defaultChangeableTemplateParameterAssigns
    ) {
    }

    /**
     * These parameters are not defined just once. They can change later or they defined based on if/else/while
     * conditions.
     *
     * @return array<ParameterAssign|AlwaysTemplateParameterAssign>
     */
    public function getNonSingleParameterAssigns(): array
    {
        return array_merge(
            $this->conditionalTemplateParameterAssign,
            $this->defaultChangeableTemplateParameterAssigns
        );
    }

    /**
     * @return ParameterAssign[]
     */
    public function getConditionalTemplateParameterAssign(): array
    {
        return $this->conditionalTemplateParameterAssign;
    }

    /**
     * @return string[]
     */
    public function getConditionalVariableNames(): array
    {
        $conditionalVariableNames = [];
        foreach ($this->conditionalTemplateParameterAssign as $conditionalTemplateParameterAssign) {
            $conditionalVariableNames[] = $conditionalTemplateParameterAssign->getParameterName();
        }

        return array_unique($conditionalVariableNames);
    }

    /**
     * @return AlwaysTemplateParameterAssign[]
     */
    public function getTemplateParameterAssigns(): array
    {
        return $this->templateParameterAssigns;
    }

    /**
     * @return array<string, Expr>
     */
    public function getTemplateVariables(): array
    {
        $templateVariables = [];
        foreach ($this->templateParameterAssigns as $templateParameterAssign) {
            $templateVariables[$templateParameterAssign->getParameterName()] = $templateParameterAssign->getAssignedExpr();
        }

        foreach ($this->defaultChangeableTemplateParameterAssigns as $alwaysTemplateParameterAssign) {
            $templateVariables[$alwaysTemplateParameterAssign->getParameterName()] = $alwaysTemplateParameterAssign->getAssignedExpr();
        }

        return $templateVariables;
    }

    /**
     * @return AlwaysTemplateParameterAssign[]
     */
    public function getDefaultChangeableTemplateParameterAssigns(): array
    {
        return $this->defaultChangeableTemplateParameterAssigns;
    }
}
