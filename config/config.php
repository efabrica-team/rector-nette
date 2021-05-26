<?php

declare(strict_types=1);

use Rector\Core\NonPhpFile\Rector\RenameClassNonPhpRector;
use Rector\Nette\Rector\Latte\RenameMethodLatteRector;
use Rector\Nette\Rector\Neon\RenameMethodNeonRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Rector\\Nette\\', __DIR__ . '/../src')
        ->exclude([
            __DIR__ . '/../src/Contract',
            __DIR__ . '/../src/Rector',
            __DIR__ . '/../src/ValueObject',
            __DIR__ . '/../src/Kdyby/Rector',
            __DIR__ . '/../src/Kdyby/ValueObject',
        ]);
    $services->set(RenameClassNonPhpRector::class);
    $services->set(RenameMethodNeonRector::class);
    $services->set(RenameMethodLatteRector::class);
};
