<?php

declare(strict_types=1);

use Rector\Nette\Rector\Property\NetteInjectToConstructorInjectionRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(NetteInjectToConstructorInjectionRector::class);
};
