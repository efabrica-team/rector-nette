<?php

declare(strict_types=1);

namespace Rector\Nette\Rector\Property;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTagRemover;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\ValueObject\PhpVersionFeature;
use Rector\Nette\NodeAnalyzer\NetteInjectPropertyAnalyzer;
use Rector\Nette\NodeAnalyzer\PropertyUsageAnalyzer;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\PostRector\Collector\PropertyToAddCollector;
use Rector\PostRector\ValueObject\PropertyMetadata;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Covers these cases:
 * - https://doc.nette.org/en/2.4/di-usage#toc-inject-annotations
 * - https://github.com/Kdyby/Autowired/blob/master/docs/en/index.md#autowired-properties
 *
 * @see \Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\NetteInjectToConstructorInjectionRectorTest
 */
final class NetteInjectToConstructorInjectionRector extends AbstractRector
{
    public function __construct(
        private PropertyUsageAnalyzer $propertyUsageAnalyzer,
        private NetteInjectPropertyAnalyzer $netteInjectPropertyAnalyzer,
        private PhpDocTagRemover $phpDocTagRemover,
        private PropertyToAddCollector $propertyToAddCollector
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
        if ($injectTagNode instanceof \PHPStan\PhpDocParser\Ast\Node) {
            $this->phpDocTagRemover->removeTagValueFromNode($phpDocInfo, $injectTagNode);
        }

        $this->changePropertyVisibility($property);

        $class = $property->getAttribute(AttributeKey::CLASS_NODE);
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
