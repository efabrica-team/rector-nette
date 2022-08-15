<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\NonPhpFile\Rector\RenameClassNonPhpRector;
use RectorNette\Rector\Latte\RenameMethodLatteRector;

return static function (RectorConfig $rectorConfig): void {
    $services = $rectorConfig->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('RectorNette\\', __DIR__ . '/../src')
        ->exclude([
            __DIR__ . '/../src/Contract',
            __DIR__ . '/../src/Rector',
            __DIR__ . '/../src/ValueObject',
        ]);

    $rectorConfig->rule(RenameClassNonPhpRector::class);
    $rectorConfig->rule(RenameMethodLatteRector::class);
};
