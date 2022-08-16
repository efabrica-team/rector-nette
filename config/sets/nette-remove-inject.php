<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorNette\Rector\Property\NetteInjectToConstructorInjectionRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(NetteInjectToConstructorInjectionRector::class);
};
