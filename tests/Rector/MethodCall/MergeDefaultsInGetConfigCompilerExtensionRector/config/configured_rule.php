<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorNette\Rector\MethodCall\MergeDefaultsInGetConfigCompilerExtensionRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->rule(MergeDefaultsInGetConfigCompilerExtensionRector::class);
};
