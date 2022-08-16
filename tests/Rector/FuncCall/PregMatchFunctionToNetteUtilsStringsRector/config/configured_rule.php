<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorNette\Rector\FuncCall\PregMatchFunctionToNetteUtilsStringsRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->rule(PregMatchFunctionToNetteUtilsStringsRector::class);
};
