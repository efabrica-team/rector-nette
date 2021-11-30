<?php

declare(strict_types=1);

use Rector\Transform\Rector\Class_\ParentClassToTraitsRector;
use Rector\Transform\ValueObject\ParentClassToTraits;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

// @see https://doc.nette.org/en/2.4/migration-2-4#toc-nette-smartobject
return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ParentClassToTraitsRector::class)
        ->configure([new ParentClassToTraits('Nette\Object', ['Nette\SmartObject'])]);
};
