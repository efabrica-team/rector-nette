<?php

declare(strict_types=1);

namespace Rector\Nette\NodeFinder;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Type\ObjectType;
use Rector\NodeTypeResolver\NodeTypeResolver;

final class FormFinder
{
    public function __construct(
        private NodeTypeResolver $nodeTypeResolver
    ) {
    }

    public function findFormVariable(Class_ $class): ?Variable
    {
        foreach ($class->getMethods() as $method) {
            foreach ($method->stmts ?: [] as $stmt) {
                if (! $stmt instanceof Expression) {
                    continue;
                }

                if (! $stmt->expr instanceof Assign) {
                    continue;
                }

                $var = $stmt->expr->var;
                $expr = $stmt->expr->expr;

                if (! $var instanceof Variable) {
                    continue;
                }

                if (! $this->nodeTypeResolver->isObjectType($expr, new ObjectType('Nette\Forms\Form'))) {
                    continue;
                }

                return $var;
            }
        }
        return null;
    }

    /**
     * @return array<string, array{type: string, required: bool}>
     */
    public function findFormFields(Class_ $class, Variable $form): array
    {
        $formFields = [];
        foreach ($class->getMethods() as $method) {
            foreach ($method->stmts ?: [] as $stmt) {
                if (! $stmt instanceof Expression) {
                    continue;
                }

                $methodCall = null;
                if ($stmt->expr instanceof MethodCall) {
                    $methodCall = $stmt->expr;
                } elseif ($stmt->expr instanceof Assign && $stmt->expr->expr instanceof MethodCall) {
                    $methodCall = $stmt->expr->expr;
                }
                if ($methodCall === null) {
                    continue;
                }

                $addFieldMethodCall = $this->findAddFieldMethodCall($methodCall);
                if (! $addFieldMethodCall) {
                    continue;
                }
                $methodCallVariable = $this->findMethodCallVariable($addFieldMethodCall);
                if ($methodCallVariable->name !== $form->name) {
                    continue;
                }

                $arg = $addFieldMethodCall->args[0] ?? null;
                if (! $arg) {
                    continue;
                }
                $name = $arg->value;
                if ($name instanceof String_) {
                    $formFields[$name->value] = [
                        'type' => $this->resolveFieldType($addFieldMethodCall->name->name),
                        'required' => $this->isFieldRequired($methodCall),
                    ];
                }
            }
        }
        return $formFields;
    }

    public function findOnSuccessCallback(Class_ $class, Variable $form): ?Expr
    {
        foreach ($class->getMethods() as $method) {
            foreach ($method->stmts ?: [] as $stmt) {
                if (! $stmt instanceof Expression) {
                    continue;
                }
                if (! $stmt->expr instanceof Assign) {
                    continue;
                }

                if (! $stmt->expr->var instanceof ArrayDimFetch) {
                    continue;
                }

                /** @var ArrayDimFetch $arrayDimFetch */
                $arrayDimFetch = $stmt->expr->var;
                if (! $arrayDimFetch->var instanceof PropertyFetch) {
                    continue;
                }

                if (! $arrayDimFetch->var->var instanceof Variable) {
                    continue;
                }

                if ($arrayDimFetch->var->var->name !== $form->name) {
                    continue;
                }

                if (! $arrayDimFetch->var->name instanceof Identifier) {
                    continue;
                }

                if ($arrayDimFetch->var->name->name !== 'onSuccess') {
                    continue;
                }

                return $stmt->expr->expr;
            }
        }
        return null;
    }

    public function findOnSuccessCallbackValuesParam(Class_ $class, Expr $onSuccessCallback): ?Param
    {
        if ($onSuccessCallback instanceof Closure) {
            return $onSuccessCallback->params[1] ?? null;
        }

        $methodName = null;
        if ($onSuccessCallback instanceof Array_) {
            /** @var Expr\ArrayItem|null $varPart */
            $varPart = $onSuccessCallback->items[0] ?? null;
            $methodNamePart = $onSuccessCallback->items[1] ?? null;

            if ($varPart === null || $methodNamePart === null) {
                return null;
            }

            if (! $varPart->value instanceof Variable) {
                return null;
            }

            if ($varPart->value->name !== 'this') {
                return null;
            }

            if (! $methodNamePart->value instanceof String_) {
                return null;
            }

            $methodName = $methodNamePart->value->value;
        }

        if ($methodName) {
            $classMethod = $class->getMethod($methodName);
            if ($classMethod === null) {
                return null;
            }

            return $classMethod->params[1] ?? null;
        }

        return null;
    }

    private function findAddFieldMethodCall(MethodCall $methodCall): ?MethodCall
    {
        if ($methodCall->var instanceof Variable) {
            // skip submit buttons
            if ($this->nodeTypeResolver->isObjectType(
                $methodCall,
                new ObjectType('Nette\Forms\Controls\SubmitButton')
            )) {
                return null;
            }
            if ($this->nodeTypeResolver->isObjectType($methodCall, new ObjectType('Nette\Forms\Container'))) {
                return $methodCall;
            }
            // skip groups, renderers, translator etc.
            if ($this->nodeTypeResolver->isObjectType(
                $methodCall,
                new ObjectType('Nette\Forms\Controls\BaseControl')
            )) {
                return $methodCall;
            }
            return null;
        }

        if ($methodCall->var instanceof MethodCall) {
            return $this->findAddFieldMethodCall($methodCall->var);
        }
        return null;
    }

    private function findMethodCallVariable(MethodCall $methodCall): ?Variable
    {
        if ($methodCall->var instanceof Variable) {
            return $methodCall->var;
        }

        if ($methodCall->var instanceof MethodCall) {
            return $this->findMethodCallVariable($methodCall->var);
        }
        return null;
    }

    private function isFieldRequired(MethodCall $methodCall): bool
    {
        if ($methodCall->name->name === 'setRequired') {    // TODO addRule(Form:FILLED) is also required
            return true;
        }

        if ($methodCall->var instanceof MethodCall) {
            return $this->isFieldRequired($methodCall->var);
        }

        return false;
    }

    private function resolveFieldType(string $methodName): string
    {
        return match ($methodName) {
            'addInteger' => 'int',  // TODO ->addText()->addRule(integer) is also int
            'addContainer' => 'array',
            'addCheckbox' => 'bool',
            default => 'string',
        };
    }
}
