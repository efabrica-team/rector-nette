<?php

declare(strict_types=1);

namespace Rector\Nette\Kdyby\BlueprintFactory;

use PhpParser\Node\Arg;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StaticType;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Naming\Naming\VariableNaming;
use Rector\Nette\Kdyby\ValueObject\VariableWithType;
use Rector\NodeTypeResolver\NodeTypeResolver;
use Rector\StaticTypeMapper\StaticTypeMapper;

final class VariableWithTypesFactory
{
    public function __construct(
        private NodeTypeResolver $nodeTypeResolver,
        private StaticTypeMapper $staticTypeMapper,
        private VariableNaming $variableNaming
    ) {
    }

    /**
     * @param Arg[] $args
     * @return VariableWithType[]
     */
    public function createVariablesWithTypesFromArgs(array $args): array
    {
        $variablesWithTypes = [];

        foreach ($args as $arg) {
            $staticType = $this->nodeTypeResolver->getType($arg->value);
            $variableName = $this->variableNaming->resolveFromNodeAndType($arg, $staticType);
            if ($variableName === null) {
                throw new ShouldNotHappenException();
            }

            // compensate for static
            if ($staticType instanceof StaticType) {
                $staticType = new ObjectType($staticType->getClassName());
            }

            $phpParserTypeNode = $this->staticTypeMapper->mapPHPStanTypeToPhpParserNode(
                $staticType,
                \Rector\PHPStanStaticTypeMapper\Enum\TypeKind::PROPERTY()
            );
            $variablesWithTypes[] = new VariableWithType($variableName, $staticType, $phpParserTypeNode);
        }

        return $variablesWithTypes;
    }
}
