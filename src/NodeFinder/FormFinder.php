<?php

declare(strict_types=1);

namespace Rector\Nette\NodeFinder;

use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
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
            foreach ($method->stmts as $stmt) {
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

    public function findFormFields(Class_ $node, Variable $form): array
    {
        $formFields = [];
        foreach ($node->getMethods() as $method) {
            foreach ($method->stmts as $stmt) {
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

    private function findAddFieldMethodCall(MethodCall $methodCall): ?MethodCall
    {
        if ($methodCall->var instanceof Variable) {
            // skip submit buttons
            if ($this->nodeTypeResolver->isObjectType($methodCall, new ObjectType('Nette\Forms\Controls\SubmitButton'))) {
                return null;
            }
            if ($this->nodeTypeResolver->isObjectType($methodCall, new ObjectType('Nette\Forms\Container'))) {
                return $methodCall;
            }
            // skip groups, renderers, translator etc.
            if ($this->nodeTypeResolver->isObjectType($methodCall, new ObjectType('Nette\Forms\Controls\BaseControl'))) {
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
