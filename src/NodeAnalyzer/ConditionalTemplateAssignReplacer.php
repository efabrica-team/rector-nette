<?php

declare(strict_types=1);

namespace Rector\Nette\NodeAnalyzer;

use PhpParser\Node\Expr\Variable;
use Rector\Nette\ValueObject\TemplateParametersAssigns;

/**
 * Replaces:
 *
 * if (...) { $this->template->key = 'some'; } else { $this->template->key = 'another'; }
 *
 * ↓
 *
 * if (...) { $key = 'some'; } else { $key = 'another'; }
 */
final class ConditionalTemplateAssignReplacer
{
    public function processClassMethod(TemplateParametersAssigns $templateParametersAssigns): void
    {
        foreach ($templateParametersAssigns->getConditionalTemplateParameterAssign() as $conditionalTemplateParameterAssign) {
            $assign = $conditionalTemplateParameterAssign->getAssign();
            $assign->var = new Variable($conditionalTemplateParameterAssign->getParameterName());
        }
    }
}
