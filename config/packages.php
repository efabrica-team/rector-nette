<?php

declare(strict_types=1);

use Nette\Neon\Decoder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Rector\\Nette\\NeonParser\\', __DIR__ . '/../packages/NeonParser')
        ->exclude([
            __DIR__ . '/../packages/NeonParser/NeonNodeTraverser.php',
            __DIR__ . '/../packages/NeonParser/Node',
        ]);

    $services->set(Decoder::class);
};
