<?php

declare(strict_types=1);

namespace Rector\Nette\Kdyby\Naming;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Cast;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ThisType;
use PHPStan\Type\Type;
use Rector\Core\Exception\NotImplementedYetException;
use Rector\Core\PhpParser\Node\Value\ValueResolver;
use Rector\Core\Util\StaticInstanceOf;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;
use Stringy\Stringy;

final class VariableNaming
{
    /**
     * @var NodeNameResolver
     */
    private $nodeNameResolver;

    /**
     * @var ValueResolver
     */
    private $valueResolver;

    /**
     * @var NodeTypeResolver
     */
    private $nodeTypeResolver;

    public function __construct(
        NodeNameResolver $nodeNameResolver,
        ValueResolver $valueResolver,
        NodeTypeResolver $nodeTypeResolver
    ) {
        $this->nodeNameResolver = $nodeNameResolver;
        $this->valueResolver = $valueResolver;
        $this->nodeTypeResolver = $nodeTypeResolver;
    }

    public function resolveFromNode(Node $node): ?string
    {
        $nodeType = $this->nodeTypeResolver->getStaticType($node);
        return $this->resolveFromNodeAndType($node, $nodeType);
    }

    public function resolveFromNodeAndType(Node $node, Type $type): ?string
    {
        $variableName = $this->resolveBareFromNode($node);
        if ($variableName === null) {
            return null;
        }

        // adjust static to specific class
        if ($variableName === 'this' && $type instanceof ThisType) {
            $shortClassName = $this->nodeNameResolver->getShortName($type->getClassName());
            $variableName = lcfirst($shortClassName);
        }

        $stringy = new Stringy($variableName);
        return (string) $stringy->camelize();
    }

    private function resolveBareFromNode(Node $node): ?string
    {
        $node = $this->unwrapNode($node);

        if ($node instanceof ArrayDimFetch) {
            return $this->resolveParamNameFromArrayDimFetch($node);
        }

        if ($node instanceof PropertyFetch) {
            return $this->resolveFromPropertyFetch($node);
        }

        if ($node !== null && StaticInstanceOf::isOneOf(
            $node,
            [MethodCall::class, NullsafeMethodCall::class, StaticCall::class])) {
            return $this->resolveFromMethodCall($node);
        }

        if ($node instanceof New_) {
            return $this->resolveFromNew($node);
        }

        if ($node instanceof FuncCall) {
            return $this->resolveFromNode($node->name);
        }

        if (! $node instanceof Node) {
            throw new NotImplementedYetException();
        }

        $paramName = $this->nodeNameResolver->getName($node);
        if ($paramName !== null) {
            return $paramName;
        }

        if ($node instanceof String_) {
            return $node->value;
        }

        return null;
    }

    private function resolveBareFuncCallArgumentName(FuncCall $funcCall, string $fallbackName, string $suffix): string
    {
        $argumentValue = $funcCall->args[0]->value;
        if ($argumentValue instanceof MethodCall || $argumentValue instanceof StaticCall) {
            $name = $this->nodeNameResolver->getName($argumentValue->name);
        } else {
            $name = $this->nodeNameResolver->getName($argumentValue);
        }

        if ($name === null) {
            return $fallbackName;
        }

        return $name . $suffix;
    }

    private function unwrapNode(Node $node): ?Node
    {
        if ($node instanceof Arg) {
            return $node->value;
        }

        if ($node instanceof Cast) {
            return $node->expr;
        }

        if ($node instanceof Ternary) {
            return $node->if;
        }

        return $node;
    }

    private function resolveParamNameFromArrayDimFetch(ArrayDimFetch $arrayDimFetch): ?string
    {
        while ($arrayDimFetch instanceof ArrayDimFetch) {
            if ($arrayDimFetch->dim instanceof Scalar) {
                $valueName = $this->nodeNameResolver->getName($arrayDimFetch->var);
                $dimName = $this->valueResolver->getValue($arrayDimFetch->dim);

                $stringy = new Stringy($dimName);
                $dimName = (string) $stringy->upperCamelize();

                return $valueName . $dimName;
            }

            $arrayDimFetch = $arrayDimFetch->var;
        }

        return $this->resolveBareFromNode($arrayDimFetch);
    }

    private function resolveFromPropertyFetch(PropertyFetch $propertyFetch): string
    {
        $varName = $this->nodeNameResolver->getName($propertyFetch->var);
        if (! is_string($varName)) {
            throw new NotImplementedYetException();
        }

        $propertyName = $this->nodeNameResolver->getName($propertyFetch->name);
        if (! is_string($propertyName)) {
            throw new NotImplementedYetException();
        }

        if ($varName === 'this') {
            return $propertyName;
        }

        return $varName . ucfirst($propertyName);
    }

    /**
     * @param MethodCall|NullsafeMethodCall|StaticCall $node
     */
    private function resolveFromMethodCall(Node $node): ?string
    {
        if ($node->name instanceof MethodCall) {
            return $this->resolveFromMethodCall($node->name);
        }

        $methodName = $this->nodeNameResolver->getName($node->name);
        if (! is_string($methodName)) {
            return null;
        }

        return $methodName;
    }

    private function resolveFromNew(New_ $new): string
    {
        if ($new->class instanceof Name) {
            $className = $this->nodeNameResolver->getName($new->class);
            if ($className === null) {
                throw new NotImplementedYetException();
            }

            return $this->nodeNameResolver->getShortName($className);
        }

        throw new NotImplementedYetException();
    }
}
