<?php

declare(strict_types=1);

use Rector\Core\ValueObject\Visibility;
use Rector\Nette\Kdyby\Rector\ClassMethod\ChangeNetteEventNamesInGetSubscribedEventsRector;
use Rector\Nette\Kdyby\Rector\MethodCall\ReplaceEventManagerWithEventSubscriberRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Visibility\Rector\ClassMethod\ChangeMethodVisibilityRector;
use Rector\Visibility\ValueObject\ChangeMethodVisibility;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ChangeNetteEventNamesInGetSubscribedEventsRector::class);
    $services->set(ReplaceEventManagerWithEventSubscriberRector::class);

    $services->set(ChangeMethodVisibilityRector::class)
        ->configure([
            new ChangeMethodVisibility('Kdyby\Events\Subscriber', 'getSubscribedEvents', Visibility::STATIC),
        ]);

    $services->set(RenameClassRector::class)
        ->configure([
            'Kdyby\Events\Subscriber' => 'Symfony\Component\EventDispatcher\EventSubscriberInterface',
            'Kdyby\Events\EventManager' => 'Symfony\Contracts\EventDispatcher\EventDispatcherInterface',
        ]);
};
