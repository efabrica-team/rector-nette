<?php

declare(strict_types=1);

namespace Rector\Nette\FormControlTypeResolver;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\NodeManipulator\MethodCallManipulator;
use Rector\Core\PhpParser\Node\Value\ValueResolver;
use Rector\Nette\Contract\FormControlTypeResolverInterface;
use Rector\Nette\Enum\NetteFormMethodNameToControlType;
use Rector\NodeNameResolver\NodeNameResolver;

final class OnVariableMethodCallsFormControlTypeResolver implements FormControlTypeResolverInterface
{
    public function __construct(
        private MethodCallManipulator $methodCallManipulator,
        private NodeNameResolver $nodeNameResolver,
        private ValueResolver $valueResolver
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function resolve(Node $node): array
    {
        if (! $node instanceof Variable) {
            return [];
        }

        $onFormMethodCalls = $this->methodCallManipulator->findMethodCallsOnVariable($node);

        $methodNamesByInputNames = [];
        foreach ($onFormMethodCalls as $onFormMethodCall) {
            $methodName = $this->nodeNameResolver->getName($onFormMethodCall->name);
            if ($methodName === null) {
                continue;
            }

            if (! isset(NetteFormMethodNameToControlType::METHOD_NAME_TO_CONTROL_TYPE[$methodName])) {
                continue;
            }

            if (! isset($onFormMethodCall->args[0])) {
                continue;
            }

            $addedInputName = $this->valueResolver->getValue($onFormMethodCall->args[0]->value);
            if (! is_string($addedInputName)) {
                throw new ShouldNotHappenException();
            }

            $methodNamesByInputNames[$addedInputName] = $methodName;
        }

        return $methodNamesByInputNames;
    }
}
