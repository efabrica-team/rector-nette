<?php

declare(strict_types=1);

use Rector\Core\ValueObject\Visibility;
use Rector\Nette\Kdyby\Rector\ClassMethod\ChangeNetteEventNamesInGetSubscribedEventsRector;
use Rector\Nette\Kdyby\Rector\MethodCall\ReplaceEventManagerWithEventSubscriberRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Visibility\Rector\ClassMethod\ChangeMethodVisibilityRector;
use Rector\Visibility\ValueObject\ChangeMethodVisibility;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ChangeNetteEventNamesInGetSubscribedEventsRector::class);
    $services->set(ReplaceEventManagerWithEventSubscriberRector::class);

    $services->set(ChangeMethodVisibilityRector::class)
        ->call('configure', [[
            ChangeMethodVisibilityRector::METHOD_VISIBILITIES => ValueObjectInliner::inline([
                new ChangeMethodVisibility('Kdyby\Events\Subscriber', 'getSubscribedEvents', Visibility::STATIC),
            ]),
        ]]);

    $services->set(RenameClassRector::class)
        ->call('configure', [[
            RenameClassRector::OLD_TO_NEW_CLASSES => [
                'Kdyby\Events\Subscriber' => 'Symfony\Component\EventDispatcher\EventSubscriberInterface',
                'Kdyby\Events\EventManager' => 'Symfony\Contracts\EventDispatcher\EventDispatcherInterface',
            ],
        ]]);
};
