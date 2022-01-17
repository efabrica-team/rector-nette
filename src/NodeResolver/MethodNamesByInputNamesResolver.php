<?php

declare(strict_types=1);

namespace Rector\Nette\NodeResolver;

use PhpParser\Node;
use Rector\Nette\Contract\FormControlTypeResolverInterface;

final class MethodNamesByInputNamesResolver
{
    /**
     * @param FormControlTypeResolverInterface[] $formControlTypeResolvers
     */
    public function __construct(
        private readonly array $formControlTypeResolvers
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function resolveExpr(Node $node): array
    {
        $methodNamesByInputNames = [];

        foreach ($this->formControlTypeResolvers as $formControlTypeResolver) {
            $currentMethodNamesByInputNames = $formControlTypeResolver->resolve($node);
            $methodNamesByInputNames = [...$methodNamesByInputNames, ...$currentMethodNamesByInputNames];
        }

        return $methodNamesByInputNames;
    }
}
