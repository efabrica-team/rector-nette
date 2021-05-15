<?php

declare(strict_types=1);

namespace Rector\Nette\FormControlTypeResolver;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\TypeWithClassName;
use Rector\Core\Reflection\FunctionLikeReflectionParser;
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
        private FunctionLikeReflectionParser $functionLikeReflectionParser,
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

        $classMethod = $this->nodeRepository->findClassMethodByMethodCall($node);
        if (! $classMethod instanceof ClassMethod) {
            $classMethod = $this->resolveReflectionClassMethod($node, $methodName);
            if (! $classMethod instanceof ClassMethod) {
                return [];
            }
        }

        $classReflection = $this->resolveClassReflectionByMethodCall($node);
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        $returnedType = $this->nodeTypeResolver->getStaticType($node);
        if (! $returnedType instanceof TypeWithClassName) {
            return [];
        }

        $constructorClassMethod = $this->nodeRepository->findClassMethod(
            $returnedType->getClassName(),
            MethodName::CONSTRUCT
        );

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

        return $this->functionLikeReflectionParser->parseMethodReflection($methodReflection);
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
        return $this->functionLikeReflectionParser->parseMethodReflection($methodReflection);
    }

    private function resolveClassReflectionByMethodCall(MethodCall $methodCall): ?ClassReflection
    {
        $callerClassName = $this->nodeRepository->resolveCallerClassName($methodCall);
        if ($callerClassName === null) {
            return null;
        }

        if (! $this->reflectionProvider->hasClass($callerClassName)) {
            return null;
        }

        return $this->reflectionProvider->getClass($callerClassName);
    }
}
