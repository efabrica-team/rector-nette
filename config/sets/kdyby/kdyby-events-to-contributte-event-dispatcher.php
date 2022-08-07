<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\Visibility;
use Rector\Nette\Kdyby\Rector\MethodCall\ReplaceEventManagerWithEventSubscriberRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Visibility\Rector\ClassMethod\ChangeMethodVisibilityRector;
use Rector\Visibility\ValueObject\ChangeMethodVisibility;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(ReplaceEventManagerWithEventSubscriberRector::class);

    $rectorConfig->ruleWithConfiguration(ChangeMethodVisibilityRector::class, [
        new ChangeMethodVisibility('Kdyby\Events\Subscriber', 'getSubscribedEvents', Visibility::STATIC),
    ]);

    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Kdyby\Events\Subscriber' => 'Symfony\Component\EventDispatcher\EventSubscriberInterface',
        'Kdyby\Events\EventManager' => 'Symfony\Contracts\EventDispatcher\EventDispatcherInterface',
    ]);
};
