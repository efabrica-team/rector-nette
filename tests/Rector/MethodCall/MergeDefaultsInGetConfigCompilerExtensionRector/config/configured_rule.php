<?php

declare(strict_types=1);

use Rector\Nette\Rector\MethodCall\MergeDefaultsInGetConfigCompilerExtensionRector;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');
    $services = $rectorConfig->services();

    $services->set(MergeDefaultsInGetConfigCompilerExtensionRector::class);
};
