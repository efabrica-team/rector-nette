<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorNette\Rector\MethodCall\BuilderExpandToHelperExpandRector;
use RectorNette\Rector\MethodCall\SetClassWithArgumentToSetFactoryRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(SetClassWithArgumentToSetFactoryRector::class);
    $rectorConfig->rule(BuilderExpandToHelperExpandRector::class);
};
