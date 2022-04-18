<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\NonPhpFile\Rector\RenameClassNonPhpRector;
use Rector\Nette\Rector\Latte\RenameMethodLatteRector;
use Rector\Nette\Rector\Neon\RenameMethodNeonRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/packages.php');

    $services = $rectorConfig->services();

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

    $rectorConfig->rule(RenameClassNonPhpRector::class);
    $rectorConfig->rule(RenameMethodNeonRector::class);
    $rectorConfig->rule(RenameMethodLatteRector::class);
};
