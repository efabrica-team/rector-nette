<?php

declare(strict_types=1);

namespace Rector\Nette\Kdyby\Naming;

use Nette\Utils\Strings;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Rector\CodingStyle\Naming\ClassNaming;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\SmartFileSystem\SmartFileInfo;

final class EventClassNaming
{
    /**
     * @var string
     */
    private const EVENT = 'Event';

    public function __construct(
        private ClassNaming $classNaming,
        private NodeNameResolver $nodeNameResolver
    ) {
    }

    /**
     * "App\SomeNamespace\SomeClass::onUpload" → "App\SomeNamespace\Event\SomeClassUploadEvent"
     */
    public function createEventClassNameFromMethodCall(MethodCall $methodCall): string
    {
        $shortEventClassName = $this->getShortEventClassName($methodCall);

        /** @var string $className */
        $className = $methodCall->getAttribute(AttributeKey::CLASS_NAME);

        return $this->prependShortClassEventWithNamespace($shortEventClassName, $className);
    }

    public function resolveEventFileLocationFromMethodCall(MethodCall $methodCall): string
    {
        $shortEventClassName = $this->getShortEventClassName($methodCall);

        $scope = $methodCall->getAttribute(AttributeKey::SCOPE);
        if (! $scope instanceof Scope) {
            throw new ShouldNotHappenException();
        }

        $directory = dirname($scope->getFile());

        return $directory . DIRECTORY_SEPARATOR . self::EVENT . DIRECTORY_SEPARATOR . $shortEventClassName . '.php';
    }

    public function resolveEventFileLocationFromClassNameAndFileInfo(
        string $className,
        SmartFileInfo $smartFileInfo
    ): string {
        $shortClassName = $this->nodeNameResolver->getShortName($className);

        return $smartFileInfo->getPath() . DIRECTORY_SEPARATOR . self::EVENT . DIRECTORY_SEPARATOR . $shortClassName . '.php';
    }

    public function createEventClassNameFromClassAndProperty(string $className, string $methodName): string
    {
        $shortEventClass = $this->createShortEventClassNameFromClassAndProperty($className, $methodName);

        return $this->prependShortClassEventWithNamespace($shortEventClass, $className);
    }

    public function createEventClassNameFromClassPropertyReference(string $classAndPropertyName): string
    {
        [$class, $property] = explode('::', $classAndPropertyName);

        $shortEventClass = $this->createShortEventClassNameFromClassAndProperty($class, $property);

        return $this->prependShortClassEventWithNamespace($shortEventClass, $class);
    }

    private function getShortEventClassName(MethodCall $methodCall): string
    {
        /** @var string $methodName */
        $methodName = $this->nodeNameResolver->getName($methodCall->name);

        /** @var string $className */
        $className = $methodCall->getAttribute(AttributeKey::CLASS_NAME);

        return $this->createShortEventClassNameFromClassAndProperty($className, $methodName);
    }

    private function prependShortClassEventWithNamespace(string $shortEventClassName, string $orinalClassName): string
    {
        $namespaceAbove = Strings::before($orinalClassName, '\\', -1);

        return $namespaceAbove . '\\Event\\' . $shortEventClassName;
    }

    /**
     * TomatoMarket, onBuy → TomatoMarketBuyEvent
     */
    private function createShortEventClassNameFromClassAndProperty(string $class, string $property): string
    {
        $shortClassName = $this->classNaming->getShortName($class);

        // "onMagic" => "Magic"
        $shortPropertyName = Strings::substring($property, strlen('on'));

        return $shortClassName . $shortPropertyName . self::EVENT;
    }
}
