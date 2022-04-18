<?php

declare(strict_types=1);

namespace Rector\Nette\NodeAnalyzer;

use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\ValueObject\MethodName;
use Rector\FamilyTree\NodeAnalyzer\ClassChildAnalyzer;
use Rector\NodeTypeResolver\Node\AttributeKey;

final class NetteInjectPropertyAnalyzer
{
    public function __construct(
        private readonly ClassChildAnalyzer $classChildAnalyzer
    ) {
    }

    public function canBeRefactored(Property $property, PhpDocInfo $phpDocInfo): bool
    {
        if (! $phpDocInfo->hasByName('inject')) {
            throw new ShouldNotHappenException();
        }

        // it needs @var tag as well, to get the type - faster, put first :)
        if (! $this->isKnownPropertyType($phpDocInfo, $property)) {
            return false;
        }

        $scope = $property->getAttribute(AttributeKey::SCOPE);
        if (! $scope instanceof Scope) {
            return false;
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        if ($classReflection->isAbstract()) {
            return false;
        }

        if ($classReflection->isAnonymous()) {
            return false;
        }

        if ($this->classChildAnalyzer->hasChildClassMethod($classReflection, MethodName::CONSTRUCT)) {
            return false;
        }

        return $this->hasNoOrEmptyParamParentConstructor($classReflection);
    }

    public function hasNoOrEmptyParamParentConstructor(ClassReflection $classReflection): bool
    {
        $parentClassMethods = $this->classChildAnalyzer->resolveParentClassMethods(
            $classReflection,
            MethodName::CONSTRUCT
        );
        if ($parentClassMethods === []) {
            return true;
        }

        // are there parent ctors? - has empty constructor params? it can be refactored
        foreach ($parentClassMethods as $parentClassMethod) {
            $parametersAcceptor = ParametersAcceptorSelector::selectSingle($parentClassMethod->getVariants());
            if ($parametersAcceptor->getParameters() !== []) {
                return false;
            }
        }

        return true;
    }

    private function isKnownPropertyType(PhpDocInfo $phpDocInfo, Property $property): bool
    {
        if ($phpDocInfo->getVarTagValueNode() !== null) {
            return true;
        }

        return $property->type !== null;
    }
}
