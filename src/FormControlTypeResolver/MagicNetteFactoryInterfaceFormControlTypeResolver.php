<?php

declare(strict_types=1);

namespace Rector\Nette\FormControlTypeResolver;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\TypeWithClassName;
use Rector\Core\Reflection\ReflectionAstResolver;
use Rector\Core\ValueObject\MethodName;
use Rector\Nette\Contract\FormControlTypeResolverInterface;
use Rector\Nette\NodeResolver\MethodNamesByInputNamesResolver;
use Rector\NodeCollector\NodeCollector\NodeRepository;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;

final class MagicNetteFactoryInterfaceFormControlTypeResolver implements FormControlTypeResolverInterface
{
    private MethodNamesByInputNamesResolver $methodNamesByInputNamesResolver;

    public function __construct(
        private NodeRepository $nodeRepository,
        private NodeNameResolver $nodeNameResolver,
        private NodeTypeResolver $nodeTypeResolver,
        private ReflectionAstResolver $functionLikeReflectionParser,
        private ReflectionProvider $reflectionProvider
    ) {
    }

    /**
     * @required
     */
    public function autowireMagicNetteFactoryInterfaceFormControlTypeResolver(
        MethodNamesByInputNamesResolver $methodNamesByInputNamesResolver
    ): void {
        $this->methodNamesByInputNamesResolver = $methodNamesByInputNamesResolver;
    }

    /**
     * @return array<string, string>
     */
    public function resolve(Node $node): array
    {
        if (! $node instanceof MethodCall) {
            return [];
        }

        // skip constructor, handled elsewhere
        if ($this->nodeNameResolver->isName($node->name, MethodName::CONSTRUCT)) {
            return [];
        }

        $methodName = $this->nodeNameResolver->getName($node->name);
        if ($methodName === null) {
            return [];
        }

        $classMethod = $this->resolveReflectionClassMethod($node, $methodName);

        $classReflection = $this->resolveClassReflectionByMethodCall($node);
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        $returnedType = $this->nodeTypeResolver->getStaticType($node);
        if (! $returnedType instanceof TypeWithClassName) {
            return [];
        }

        $class = $this->nodeRepository->findClass($returnedType->getClassName());
        if (! $class instanceof ClassLike) {
            return [];
        }

        $constructorClassMethod = $class->getMethod(MethodName::CONSTRUCT);

        if (! $constructorClassMethod instanceof ClassMethod) {
            $constructorClassMethod = $this->resolveReflectionClassMethodFromClassNameAndMethod(
                $returnedType->getClassName(),
                MethodName::CONSTRUCT
            );
            if (! $classMethod instanceof ClassMethod) {
                return [];
            }
        }

        if (! $constructorClassMethod instanceof ClassMethod) {
            return [];
        }

        return $this->methodNamesByInputNamesResolver->resolveExpr($constructorClassMethod);
    }

    private function resolveReflectionClassMethod(MethodCall $methodCall, string $methodName): ?ClassMethod
    {
        $classReflection = $this->resolveClassReflectionByMethodCall($methodCall);
        if (! $classReflection instanceof ClassReflection) {
            return null;
        }

        $methodReflection = $classReflection->getNativeMethod($methodName);

        return $this->functionLikeReflectionParser->resolveMethodReflection($methodReflection);
    }

    private function resolveReflectionClassMethodFromClassNameAndMethod(
        string $className,
        string $methodName
    ): ?ClassMethod {
        if (! $this->reflectionProvider->hasClass($className)) {
            return null;
        }

        $classReflection = $this->reflectionProvider->getClass($className);
        $methodReflection = $classReflection->getNativeMethod($methodName);
        return $this->functionLikeReflectionParser->resolveMethodReflection($methodReflection);
    }

    private function resolveClassReflectionByMethodCall(MethodCall $methodCall): ?ClassReflection
    {
        $callerType = $this->nodeTypeResolver->resolve($methodCall->var);
        if (! $callerType instanceof TypeWithClassName) {
            return null;
        }

        $callerClassName = $callerType->getClassName();
        if (! $this->reflectionProvider->hasClass($callerClassName)) {
            return null;
        }

        return $this->reflectionProvider->getClass($callerClassName);
    }
}
