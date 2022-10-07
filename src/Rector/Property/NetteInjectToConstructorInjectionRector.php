<?php

declare(strict_types=1);

namespace RectorNette\Rector\Property;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use PHPStan\PhpDocParser\Ast\Node as PHPStanNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTagRemover;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\Php\PhpVersionProvider;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\ValueObject\PhpVersionFeature;
use Rector\PostRector\Collector\PropertyToAddCollector;
use Rector\PostRector\ValueObject\PropertyMetadata;
use Rector\Privatization\NodeManipulator\VisibilityManipulator;
use RectorNette\NodeAnalyzer\NetteInjectPropertyAnalyzer;
use RectorNette\NodeAnalyzer\PropertyUsageAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Covers these cases:
 * - https://doc.nette.org/en/2.4/di-usage#toc-inject-annotations
 * - https://github.com/Kdyby/Autowired/blob/master/docs/en/index.md#autowired-properties
 *
 * @see \RectorNette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\NetteInjectToConstructorInjectionRectorTest
 */
final class NetteInjectToConstructorInjectionRector extends AbstractRector
{
    public function __construct(
        private PropertyUsageAnalyzer $propertyUsageAnalyzer,
        private NetteInjectPropertyAnalyzer $netteInjectPropertyAnalyzer,
        private PhpDocTagRemover $phpDocTagRemover,
        private PropertyToAddCollector $propertyToAddCollector,
        private VisibilityManipulator $visibilityManipulator,
        private PhpVersionProvider $phpVersionProvider,
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Turns properties with `@inject` to private properties and constructor injection',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
/**
 * @var SomeService
 * @inject
 */
public $someService;
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
/**
 * @var SomeService
 */
private $someService;

public function __construct(SomeService $someService)
{
    $this->someService = $someService;
}
CODE_SAMPLE
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Property::class];
    }

    /**
     * @param Property $node
     */
    public function refactor(Node $node): ?Node
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFromNode($node);
        if (! $phpDocInfo instanceof PhpDocInfo) {
            return null;
        }

        if (! $phpDocInfo->hasByName('inject')) {
            return null;
        }

        if (! $this->netteInjectPropertyAnalyzer->canBeRefactored($node, $phpDocInfo)) {
            return null;
        }

        return $this->refactorNetteInjectProperty($phpDocInfo, $node);
    }

    private function refactorNetteInjectProperty(PhpDocInfo $phpDocInfo, Property $property): ?Property
    {
        $injectTagNode = $phpDocInfo->getByName('inject');
        if ($injectTagNode instanceof PHPStanNode) {
            $this->phpDocTagRemover->removeTagValueFromNode($phpDocInfo, $injectTagNode);
        }

        $this->changePropertyVisibility($property);

        $class = $this->betterNodeFinder->findParentType($property, Class_::class);
        if (! $class instanceof Class_) {
            throw new ShouldNotHappenException();
        }

        $propertyName = $this->nodeNameResolver->getName($property);
        $propertyType = $this->nodeTypeResolver->getType($property);

        $propertyMetadata = new PropertyMetadata($propertyName, $propertyType, $property->flags);
        $this->propertyToAddCollector->addPropertyToClass($class, $propertyMetadata);

        if ($this->phpVersionProvider->isAtLeastPhpVersion(PhpVersionFeature::PROPERTY_PROMOTION)) {
            $this->removeNode($property);
            return null;
        }

        return $property;
    }

    private function changePropertyVisibility(Property $property): void
    {
        if ($this->propertyUsageAnalyzer->isPropertyFetchedInChildClass($property)) {
            $this->visibilityManipulator->makeProtected($property);
        } else {
            $this->visibilityManipulator->makePrivate($property);
        }
    }
}
